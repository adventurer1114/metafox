<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Carbon;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\GenderTrait;
use MetaFox\Form\RelationTrait;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Models\UserRelation;
use MetaFox\User\Repositories\UserRelationRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\Yup\Yup;
use stdClass;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @todo: refactor using Form/Builder
 */
class UserProfileForm extends AbstractForm
{
    use RelationTrait;
    use GenderTrait;

    protected function prepare(): void
    {
        /** @var UserProfile $profile */
        $profile = $this->resource->profile;
        $gender  = $profile->gender;

        $user = new StdClass();
        if (!empty($profile->relation_with)) {
            $user = UserEntity::getById($profile->relation_with);
            $user = new UserEntityDetail($user);
        }

        $values = array_merge([
            'country_iso'       => $profile->country_iso,
            'country_state_id'  => $profile->country_state_id,
            'country_city_code' => $this->getCityCode(),
            'gender'            => $gender?->is_custom ? 0 : $profile->gender?->entityId(),
            'custom_gender'     => $gender?->is_custom ? $profile->gender?->entityId() : null,
            'postal_code'       => $profile->postal_code,
            'birthday'          => $profile->birthday,
            'relation'          => $profile->relation,
            'relation_with'     => $user,
            'address'           => $profile->address,
        ], resolve(ProfileRepositoryInterface::class)->denormalize($this->resource));

        $this->title(__p('user::phrase.edit_profile'))
            ->action(url_utility()->makeApiUrl("user/profile/{$this->resource->entityId()}"))
            ->setBackProps(__p('core::phrase.back'))
            ->asPut()
            ->setValue($values);
    }

    /**
     * @throws AuthenticationException
     */
    public function initialize(): void
    {
        $context              = user();
        $isBasicFieldRequired = Settings::get('user.require_basic_field', false);
        $enableRelationship   = Settings::get('user.enable_relationship_status', false);
        $minYear              = Settings::get('user.date_of_birth_start', 1900);
        $maxYear              = Settings::get('user.date_of_birth_end', Carbon::now()->year);
        $minDate              = Carbon::create($minYear);
        $maxDate              = Carbon::create($maxYear);
        $minDateString        = $minDate ? $minDate->toDateString() : $minYear;
        $maxDateString        = $maxDate ? $maxDate->endOfYear()->toDateString() : $maxYear;
        $birthdayMessage      = __p('validation.invalid_date_of_birth_between', [
            'date_start' => $minDateString,
            'date_end'   => $maxDateString,
        ]);
        $customGenders = $this->getCustomGenders($context);

        $basic = $this->addBasic(['label' => __p('user::phrase.basic_information')]);
        $basic->addFields(
            //Country State field
            Builder::countryState('country_iso')
                ->label(__p('localize::country.country'))
                ->valueType('array')
                ->setAttribute('countryFieldName', 'country_iso')
                ->setAttribute('stateFieldName', 'country_state_id')
                ->required($isBasicFieldRequired),
            //City field
            Builder::countryCity('country_city_code')
                ->label(__p('localize::country.city'))
                ->description(__p('localize::country.city_name'))
                ->searchEndpoint('user/city')
                ->searchParams([
                    'country' => ':country_iso',
                    'state'   => ':country_state_id',
                ]),
            //Address field
            Builder::text('address')
                ->label(__p('user::phrase.address')),
            //Postal code field
            Builder::text('postal_code')
                ->label(__p('user::phrase.postal_code'))
                ->placeholder('- - - - - -'),
            //Gender field
            Builder::gender('gender')
                ->label(__p('user::phrase.i_am'))
                ->required($isBasicFieldRequired)
                ->options($this->getDefaultGenders($context)),
            //Custom Gender field
            Builder::customGenders('custom_gender')
                ->label(__p('user::phrase.custom_gender'))
                ->showWhen(['and', ['eq', 'gender', 0]])
                ->options($customGenders)
                ->requiredWhen(['eq', 'gender', 0])
                ->yup(
                    Yup::when('gender')
                        ->is(0)
                        ->then(
                            Yup::number()
                                ->required(__p('validation.custom_gender_field_is_a_required_field'))
                        )
                ),
            //Birthday field
            Builder::birthday('birthday')
                ->label(__p('user::phrase.birthday'))
                ->required($isBasicFieldRequired)
                ->setAttribute('minDate', $minDateString)
                ->setAttribute('maxDate', $maxDateString)
                ->yup(
                    Yup::date()
                        ->nullable(true)
                        ->minYear((string) $minYear, $birthdayMessage)
                        ->maxYear((string) $maxYear, $birthdayMessage)
                        ->setError('typeError', __p('core::phrase.invalid_date'))
                ),
            Builder::hidden('previous_relation_type'),
        );

        if ($enableRelationship) {
            $basic->addField(
                Builder::choice('relation')
                    ->label(__p('user::phrase.relationship_status'))
                    ->setAttribute('dependField', 'relation_with')
                    ->setAttribute('disableUncheck', true)
                    ->options($this->getRelations())
            );

            if (app_active('metafox/friend')) {
                $basic->addFields(
                    Builder::friendPicker('relation_with')
                        ->placeholder(__p('friend::phrase.search_friends_by_their_name'))
                        ->setAttribute('api_endpoint', url_utility()->makeApiUrl('friend'))
                        ->showWhen(['includes', 'relation', $this->getWithRelations()])
                        ->resetWhenUnmount()
                );
            }
        }

        resolve(ProfileRepositoryInterface::class)
            ->loadEditFields($this, $this->resource);

        $this->addFooter()
            ->addFields(
                Builder::submit('submit')
                    ->label(__p('core::web.update'))
                    ->sizeLarge(),
                Builder::cancelButton('_cancel')->sizeLarge(),
            );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCityCode(): ?array
    {
        /** @var UserProfile $profile */
        $profile  = $this->resource->profile;
        $cityCode = null;
        if (!empty($profile->country_city_code)) {
            $value = is_numeric($profile->country_city_code) ? (int) $profile->country_city_code : $profile->country_city_code;

            $cityCode = [
                'label' => $profile->city_location,
                'value' => $value,
            ];
        }

        return $cityCode;
    }

    public function getRelations(): array
    {
        $repository      = resolve(UserRelationRepositoryInterface::class);
        $phpfoxRelations = $repository->getRelations();
        $data            = [];

        foreach ($phpfoxRelations as $relation) {
            /* @var UserRelation $relation */
            $data[] = [
                'value' => $relation->entityId(),
                'label' => __p($relation->phrase_var),
            ];
        }

        return $data;
    }
}
