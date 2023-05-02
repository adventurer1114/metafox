<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Carbon;
use MetaFox\Core\Support\Facades\Country;
use MetaFox\Form\GenderTrait;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Form\RelationTrait;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\Yup\Yup;
use stdClass;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class UserProfileMobileForm extends AbstractForm
{
    use RelationTrait;
    use GenderTrait;

    /**
     * @throws AuthenticationException
     */
    public function boot(UserRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $id ? $repository->find($id) : user();
    }

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

        $data = array_merge([
            'country_iso'       => $profile->country_iso,
            'country_state'     => $this->getCountryState($profile ?? null),
            'country_city_code' => $this->getCityCode(),
            'gender'            => $gender?->is_custom ? 0 : $profile->gender?->entityId(),
            'custom_gender'     => $gender?->is_custom ? $profile->gender?->entityId() : 0,
            'postal_code'       => $profile->postal_code,
            'birthday'          => $profile->birthday,
            'relation'          => $profile->relation,
            'relation_with'     => $user,
            'address'           => $profile->address,
        ], resolve(ProfileRepositoryInterface::class)->denormalize($this->resource));

        $this->title(__p('user::phrase.edit_profile_info'))
            ->action(url_utility()->makeApiUrl("user/profile/{$this->resource->entityId()}"))
            ->setBackProps(__p('core::phrase.back'))
            ->asPut()
            ->setValue($data);
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
        $countries     = Country::buildCountrySearchForm();

        $basic = $this->addBasic(['label' => __p('user::phrase.basic_information')]);
        $basic->addFields(
            //Country State field
            Builder::choice('country_iso')
                ->label(__p('localize::country.country'))
                ->options($countries)
                ->required($isBasicFieldRequired),
            Builder::countryStatePicker('country_state')
                ->label(__p('localize::country.state'))
                ->description(__p('localize::country.state_name'))
                ->searchEndpoint('user/country/state')
                ->showWhen(['and', ['truthy', 'country_iso']])
                ->searchParams([
                    'country' => ':country_iso',
                ]),
            //City field
            Builder::countryCity('country_city_code')
                ->label(__p('localize::country.city'))
                ->description(__p('localize::country.city_name'))
                ->searchEndpoint('user/city')
                ->searchParams([
                    'country' => ':country_iso',
                    'state'   => ':country_state',
                ]),
            //Address field
            Builder::text('address')
                ->label(__p('user::phrase.address')),
            //Postal code field
            Builder::text('postal_code')
                ->label(__p('user::phrase.postal_code'))
                ->placeholder('- - - - - -'),
            //Gender field
            Builder::choice('gender')
                ->label(__p('user::phrase.i_am'))
                ->required($isBasicFieldRequired)
                ->options($this->getDefaultGenders($context)),
            //Custom Gender field
            Builder::choice('custom_gender')
                ->label(__p('user::phrase.custom_gender'))
                ->showWhen(['and', ['eq', 'gender', 0]])
                ->options($customGenders)
                ->yup(
                    Yup::when('gender')
                        ->is(0)
                        ->then(
                            Yup::number()->required()
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
                        ->setAttribute('api_endpoint', 'friend')
                        ->showWhen(['includes', 'relation', $this->getWithRelations()]),
                );
            }
        }

        resolve(ProfileRepositoryInterface::class)
            ->loadEditFields($this, $this->resource, MetaFoxConstant::RESOLUTION_MOBILE);
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

    /**
     * @param  UserProfile|null   $profile
     * @return array<string>|null
     */
    protected function getCountryState(?UserProfile $profile): ?array
    {
        if (!$profile instanceof UserProfile) {
            return null;
        }

        $countryIso = $profile?->country_iso;
        $stateId    = $profile?->country_state_id;

        if (!is_string($countryIso) || !is_string($stateId)) {
            return null;
        }

        return [
            'value' => $stateId,
            'label' => Country::getCountryStateName($countryIso, $stateId),
        ];
    }
}
