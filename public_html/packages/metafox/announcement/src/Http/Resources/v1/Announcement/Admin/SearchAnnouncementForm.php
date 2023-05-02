<?php

namespace MetaFox\Announcement\Http\Resources\v1\Announcement\Admin;

use MetaFox\Announcement\Models\Announcement as Model;
use MetaFox\Announcement\Support\Facade\Announcement;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchAnnouncementForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class SearchAnnouncementForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->noHeader()
            ->action('/admincp/announcement')
            ->acceptPageParams(['q', 'style', 'start_from', 'start_to', 'created_from', 'created_to', 'role_id']);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic()->asHorizontal();
        $basic->addFields(
            Builder::text('q')
                ->forAdminSearchForm()
                ->placeholder(__p('localize::phrase.search_dot')),
            Builder::choice('style')
                ->forAdminSearchForm()
                ->label(__p('announcement::phrase.announcement_style'))
                ->options(Announcement::getStyleOptions()),
            Builder::choice('role_id')
                ->forAdminSearchForm()
                ->label(__p('core::phrase.role'))
                ->options(Announcement::getAllowedRoleOptions()),
            Builder::date('start_from')
                ->forAdminSearchForm()
                ->label(__p('announcement::phrase.start_from')),
            Builder::date('start_to')
                ->forAdminSearchForm()
                ->label(__p('announcement::phrase.start_to')),
            Builder::date('created_from')
                ->forAdminSearchForm()
                ->label(__p('announcement::phrase.created_from')),
            Builder::date('created_to')
                ->forAdminSearchForm()
                ->label(__p('announcement::phrase.created_to')),
            Builder::submit()->forAdminSearchForm(),
        );
    }
}
