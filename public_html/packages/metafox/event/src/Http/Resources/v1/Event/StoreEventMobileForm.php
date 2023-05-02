<?php

namespace MetaFox\Event\Http\Resources\v1\Event;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Carbon;
use MetaFox\Event\Http\Requests\v1\Event\CreateFormRequest;
use MetaFox\Event\Models\Event as Model;
use MetaFox\Event\Repositories\CategoryRepositoryInterface;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Event\Repositories\MemberRepositoryInterface;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
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
 * Class StoreEventMobileForm.
 * @property Model $resource
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StoreEventMobileForm extends AbstractForm
{
    /** @var bool */
    protected $isEdit = false;

    protected function memberRepository(): MemberRepositoryInterface
    {
        return resolve(MemberRepositoryInterface::class);
    }

    public function boot(CreateFormRequest $request, EventRepositoryInterface $repository, ?int $id = null): void
    {
        $data = $request->validated();

        $this->resource = new Model($data);
    }

    protected function prepare(): void
    {
        $context = user();
        $privacy = UserPrivacy::getItemPrivacySetting($context->entityId(), 'event.item_privacy');
        if ($privacy === false) {
            $privacy = MetaFoxPrivacy::EVERYONE;
        }
        $defaultCategory = Settings::get('event.default_category');

        $currentTime = Carbon::now();
        $this->title(__p('event::phrase.create_form'))
            ->action(url_utility()->makeApiUrl('event'))
            ->asPost()
            ->setValue([
                'module_id'   => 'event',
                'privacy'     => $privacy,
                'is_online'   => 0,
                'owner_id'    => $this->resource->owner_id,
                'attachments' => [],
                'start_time'  => $currentTime->toISOString(),
                'end_time'    => $currentTime->addHour()->toISOString(),
                'categories'  => [$defaultCategory],
            ]);
    }

    /**
     * @throws AuthenticationException
     */
    public function initialize(): void
    {
        $basic = $this->addBasic();

        $minNameLength = Settings::get('event.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $maxNameLength = Settings::get('event.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);
        $timeFormat    = Settings::get('event.default_timeFormat_format', 12);

        $minEventDate         = Carbon::now();
        $isDisableEventFields = $this->isDisableEventFields();
        $canManageHosts       = $this->canManageHosts();

        $basic->addFields(
            Builder::text('name')
                ->required()
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
            Builder::richTextEditor('text')
                ->required(false)
                ->label(__p('core::phrase.description'))
                ->placeholder(__p('event::phrase.add_some_content_to_your_event')),
            Builder::singlePhoto('file')
                ->label(__p('core::phrase.banner'))
                ->itemType('event')
                ->thumbnailSizes($this->resource->getSizes())
                ->previewUrl($this->resource->image),
            Builder::category('categories')
                ->multiple(true)
                ->sizeLarge()
                ->setRepository(CategoryRepositoryInterface::class)
                ->disabled($isDisableEventFields),
            Builder::switch('is_online')
                ->label(__p('event::phrase.set_online_event'))
                ->disabled($isDisableEventFields),
            Builder::text('event_url')
                ->required(false)
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
                                        ->setError('required', __p('validation.this_field_is_a_required_field'))
                                        ->setError('format', __p('event::phrase.this_field_must_be_valid_url'))
                                )
                        )
                ),
            Builder::dateTime('start_time')
                ->label(__p('event::phrase.start_time'))
                ->displayFormat($this->getDisplayFormat($timeFormat))
                ->timeFormat($timeFormat)
                ->required()
                ->disabled($isDisableEventFields)
                ->yup(
                    Yup::date()->required()
                        ->setError('required', __p('validation.this_field_is_a_required_field'))
                        ->setError('min', __p('event::phrase.the_event_time_should_be_greater_than_the_current_time'))
                ),
            Builder::dateTime('end_time')
                ->label(__p('event::phrase.end_time'))
                ->displayFormat($this->getDisplayFormat($timeFormat))
                ->required()
                ->timeFormat($timeFormat)
                ->minDateTime($isDisableEventFields ? null : $minEventDate)
                ->disabled($isDisableEventFields)
                ->yup(
                    Yup::date()
                        ->required()
                        ->min(['ref' => 'start_time'])
                        ->setError('required', __p('validation.this_field_is_a_required_field'))
                        ->setError(
                            'minDateTime',
                            __p('event::phrase.the_end_time_should_be_greater_than_the_current_time')
                        )
                        ->setError('min', __p('event::phrase.the_end_time_should_be_greater_than_the_start_time'))
                ),
        );

        if (app_active('metafox/friend')) {
            $basic->addFields(
                Builder::friendPicker('host')
                    ->placeholder(__p('event::phrase.search_hosts_by_their_name_dot'))
                    ->multiple(true)
                    ->endpoint('friend')
                    ->disabled($isDisableEventFields || !$canManageHosts)
                    ->enableWhen([
                        'neq', 'privacy', MetaFoxPrivacy::ONLY_ME,
                    ]),
            );
        }

        $basic->addFields(
            Builder::location('location')
                ->requiredWhen(['eq', 'is_online', 0])
                ->showWhen(['eq', 'is_online', 0])
                ->placeholder(__p('event::phrase.where_will_be_hosted'))
                ->disabled($isDisableEventFields)
                ->yup(
                    Yup::object()
                        ->nullable()
                        ->when(
                            Yup::when('is_online')
                                ->is(0)
                                ->then(
                                    Yup::object()
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
                                                ->required(__p('event::phrase.location_is_a_required_field'))
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
    }

    protected function isDisableEventFields(): bool
    {
        return false;
    }

    protected function canManageHosts(): bool
    {
        return true;
    }

    /**
     * @throws AuthenticationException
     */
    protected function buildPrivacyField(): AbstractField
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

    protected function getDisplayFormat(int $value): string
    {
        $displayFormat = [
            12 => MetaFoxConstant::DISPLAY_FORMAT_TIME_12,
            24 => MetaFoxConstant::DISPLAY_FORMAT_TIME_24,
        ];

        return $displayFormat[$value];
    }
}
