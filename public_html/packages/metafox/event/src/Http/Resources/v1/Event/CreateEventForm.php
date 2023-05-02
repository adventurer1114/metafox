<?php

namespace MetaFox\Event\Http\Resources\v1\Event;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Carbon;
use MetaFox\Event\Models\Event as Model;
use MetaFox\Event\Repositories\CategoryRepositoryInterface;
use MetaFox\Event\Repositories\MemberRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Html\Privacy;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreateEventForm.
 * @property Model $resource
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class CreateEventForm extends AbstractForm
{
    /** @var bool */
    protected $isEdit = false;

    protected function memberRepository(): MemberRepositoryInterface
    {
        return resolve(MemberRepositoryInterface::class);
    }

    protected function prepare(): void
    {
        $context = user();
        $privacy = UserPrivacy::getItemPrivacySetting($context->entityId(), 'event.item_privacy');
        if ($privacy === false) {
            $privacy = MetaFoxPrivacy::EVERYONE;
        }
        $defaultCategory = Settings::get('event.default_category');

        $this->title(__p('event::phrase.create_form'))
            ->action(url_utility()->makeApiUrl('event'))
            ->asPost()
            ->setBackProps(__p('core::phrase.events'))
            ->setValue([
                'module_id'   => 'event',
                'privacy'     => $privacy,
                'is_online'   => 0,
                'owner_id'    => $this->resource->owner_id,
                'attachments' => [],
                'start_time'  => Carbon::now()->toISOString(),
                'end_time'    => Carbon::now()->addHour()->toISOString(),
                'location'    => null,
                'categories'  => [$defaultCategory],
            ]);
    }

    /**
     * @throws AuthenticationException
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $minNameLength        = Settings::get('event.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $maxNameLength        = Settings::get('event.maximum_name_length', 100);
        $minEventDate         = Carbon::now()->toISOString();
        $isDisableEventFields = $this->isDisableEventFields();
        $canManageHosts       = $this->canManageHosts();

        $basic->addFields(
            Builder::text('name')
                ->required()
                ->returnKeyType('next')
                ->label(__p('event::phrase.event_name'))
                ->placeholder(__p('event::phrase.fill_in_a_name_for_your_event'))
                ->description(__p('event::phrase.maximum_length_of_characters', ['length' => $maxNameLength]))
                ->maxLength($maxNameLength)
                ->disabled($isDisableEventFields)
                ->yup(
                    Yup::string()
                        ->required()
                        ->minLength($minNameLength)
                        ->maxLength($maxNameLength)
                        ->setError('required', __p('core::phrase.title_is_a_required_field'))
                        ->setError('typeError', __p('core::phrase.title_is_a_required_field'))
                ),
            Builder::textArea('text')
                ->required(false)
                ->label(__p('core::phrase.description'))
                ->placeholder(__p('event::phrase.add_some_content_to_your_event')),
            Builder::singlePhoto()
                ->label(__p('core::phrase.banner'))
                ->itemType('event')
                ->thumbnailSizes($this->resource->getSizes())
                ->previewUrl($this->resource->image),
            Builder::attachment()
                ->itemType('event')
                ->disabled($isDisableEventFields),
            Builder::category('categories')
                ->multiple(true)
                ->sizeLarge()
                ->setRepository(CategoryRepositoryInterface::class)
                ->disabled($isDisableEventFields),
            Builder::checkbox('is_online')
                ->multiple(false)
                ->label(__p('event::phrase.set_online_event'))
                ->disabled($isDisableEventFields),
            Builder::text('event_url')
                ->returnKeyType('next')
                ->nullable(true)
                ->label(__p('event::phrase.event_url'))
                ->placeholder(__p('event::phrase.paste_your_event_url_here'))
                ->disabled($isDisableEventFields)
                ->showWhen(['eq', 'is_online', 1])
                ->requiredWhen(['eq', 'is_online', 1])
                ->yup(
                    Yup::string()
                        ->nullable()
                        ->when(
                            Yup::when('is_online')
                                ->is(1)
                                ->then(
                                    Yup::string()
                                        ->required()
                                        ->url()
                                        ->setError('required', __p('validation.this_field_is_required'))
                                        ->setError('format', __p('event::phrase.this_field_must_be_valid_url'))
                                )
                        )
                ),
            Builder::datetime('start_time')
                ->returnKeyType('next')
                ->required(true)
                ->disabled($isDisableEventFields)
                ->timeSuggestion(true)
                ->labelTimePicker(__p('event::phrase.start_time'))
                ->labelDatePicker(__p('event::phrase.start_date'))
                ->yup(
                    Yup::date()
                        ->required(__p('validation.this_field_is_required'))
                        ->setError(
                            'typeError',
                            __p('validation.date', ['attribute' => __p('event::phrase.start_date')])
                        )
                        ->setError('min', __p('event::phrase.the_event_time_should_be_greater_than_the_current_time'))
                ),
            Builder::datetime('end_time')
                ->returnKeyType('next')
                ->required(true)
                ->disabled($isDisableEventFields)
                ->timeSuggestion(true)
                ->labelTimePicker(__p('event::phrase.end_time'))
                ->labelDatePicker(__p('event::phrase.end_date'))
                ->minDateTime($isDisableEventFields ? '' : $minEventDate)
                ->yup(
                    Yup::date()
                        ->required(__p('validation.this_field_is_required'))
                        ->min(['ref' => 'start_time'])
                        ->setError('typeError', __p('validation.date', ['attribute' => __p('event::phrase.end_date')]))
                        ->setError(
                            'minDateTime',
                            __p('event::phrase.the_end_time_should_be_greater_than_the_current_time')
                        )
                        ->setError('min', __p('event::phrase.the_end_time_should_be_greater_than_the_start_time'))
                ),
        );

        if (app_active('metafox/friend')) {
            $basic->addField(
                Builder::friendPicker('host')
                    ->placeholder(__p('event::phrase.search_hosts_by_their_name_dot'))
                    ->multiple(true)
                    ->apiEndpoint(url_utility()->makeApiUrl('friend'))
                    ->disabled($isDisableEventFields || !$canManageHosts),
            );
        }

        $basic->addFields(
            Builder::location('location')
                ->returnKeyType('next')
                ->disabled($isDisableEventFields)
                ->requiredWhen(['eq', 'is_online', 0])
                ->yup(
                    Yup::object()
                        ->nullable()
                        ->when(
                            Yup::when('is_online')
                                ->is(0)
                                ->then(
                                    Yup::object()
                                        ->nullable()
                                        ->required()
                                        ->setError('required', __p('event::phrase.location_is_a_required_field'))
                                        ->setError('typeError', __p('event::phrase.location_is_a_required_field'))
                                        ->addProperty(
                                            'lat',
                                            Yup::number()
                                                ->nullable()
                                        )
                                        ->addProperty(
                                            'lng',
                                            Yup::number()
                                                ->nullable()
                                        )
                                        ->addProperty(
                                            'address',
                                            Yup::string()
                                                ->nullable()
                                        )
                                        ->addProperty(
                                            'short_name',
                                            Yup::string()
                                                ->nullable()
                                        )
                                )
                        )
                        ->addProperty(
                            'lat',
                            Yup::number()
                                ->nullable()
                        )
                        ->addProperty(
                            'lng',
                            Yup::number()
                                ->nullable()
                        )
                        ->addProperty(
                            'address',
                            Yup::string()
                                ->nullable()
                        )
                        ->addProperty(
                            'short_name',
                            Yup::string()
                                ->nullable()
                        )
                ),
            $this->buildPrivacyField(),
            Builder::hidden('module_id'),
            Builder::hidden('owner_id'),
        );

        $this->addDefaultFooter();
    }

    /**
     * @throws AuthenticationException
     */
    protected function buildPrivacyField(): Privacy
    {
        $context = user();

        return Builder::privacy('privacy')
            ->description(__p('event::phrase.control_who_can_see_this_event'))
            ->disabled($this->isDisableEventFields())
            ->showWhen([
                'or',
                [
                    'falsy',
                    'owner_id',
                ], [
                    'eq',
                    'owner_id',
                    $context->entityId(),
                ],
            ]);
    }

    protected function isDisableEventFields(): bool
    {
        return false;
    }

    protected function canManageHosts(): bool
    {
        return true;
    }
}
