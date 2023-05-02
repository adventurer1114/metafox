<?php

namespace MetaFox\Announcement\Http\Resources\v1\Announcement\Admin;

use Illuminate\Support\Carbon;
use MetaFox\Announcement\Models\Announcement as Model;
use MetaFox\Announcement\Support\Facade\Announcement;
use MetaFox\Core\Support\Facades\Country;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreAnnouncementForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverType form
 * @driverName announcement.store
 */
class StoreAnnouncementForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('announcement::phrase.add_an_announcement'))
            ->action('/admincp/announcement/announcement')
            ->asPost()
            ->setValue([
                'subject'       => '',
                'intro'         => '',
                'text'          => '',
                'is_active'     => 1,
                'can_be_closed' => 1,
                'style'         => 1,
                'start_date'    => Carbon::now()->toISOString(),
                'roles'         => [],
                'country_iso'   => '',
                'gender'        => 0,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic()->label(__p('announcement::phrase.announcement_content'));
        $basic->addFields(
        //@todo: Support multi language content
            Builder::text('subject')
                ->required()
                ->label(__p('announcement::phrase.subject_in_language', ['language' => 'English']))
                ->maxLength(MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH)
                ->yup(
                    Yup::string()
                        ->required()
                ),
            Builder::text('intro')
                ->required()
                ->label(__p('announcement::phrase.intro_in_language', ['language' => 'English']))
                ->maxLength(MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH)
                ->yup(
                    Yup::string()
                        ->required()
                ),
            Builder::textArea('text')
                ->label(__p('announcement::phrase.content_in_language', ['language' => 'English']))
                ->yup(Yup::string()),
        );

        $displayOptions = $this->addSection(['name' => 'displayOptions'])
            ->label(__p('announcement::phrase.display_options'));
        $displayOptions->addFields(
            Builder::switch('is_active')->label(__p('core::phrase.is_active')),
            Builder::switch('can_be_closed')->label(__p('announcement::phrase.can_be_closed')),
            Builder::choice('style')
                ->required()
                ->label(__p('announcement::phrase.announcement_style'))
                ->options(Announcement::getStyleOptions()),
            Builder::datetime('start_date')
                ->required()
                ->labelDatePicker(__p('announcement::phrase.start_date'))
                ->timeSuggestion(true)
                ->labelTimePicker(__p('announcement::phrase.start_time'))
                ->minDateTime(Carbon::now()->toISOString() ?? '')
                ->yup(Yup::date()),
        );

        $targetViewers = $this->addSection(['name' => 'targetViewers'])
            ->label(__p('announcement::phrase.target_viewers'));
        $targetViewers->addFields(
            Builder::choice('roles')
                ->multiple(true)
                ->disableClearable()
                ->label(__p('core::phrase.role'))
                ->options(Announcement::getAllowedRoleOptions()),
            Builder::choice('country_iso')
                ->multiple(false)
                ->disableClearable()
                ->label(__p('core::phrase.location'))
                ->options(Country::buildCountrySearchForm()),
            Builder::gender()->multiple(false)->disableClearable(),
        );

        $this->addDefaultFooter($this->resource?->entityId() > 0);
    }
}
