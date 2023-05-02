<?php

namespace MetaFox\Announcement\Http\Resources\v1\Announcement\Admin;

use Illuminate\Support\Carbon;
use MetaFox\Announcement\Models\Announcement as Model;
use MetaFox\Announcement\Repositories\AnnouncementRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateAnnouncementForm.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverType form
 * @driverName announcement.update
 */
class UpdateAnnouncementForm extends StoreAnnouncementForm
{
    public function boot(AnnouncementRepositoryInterface $repository, ?int $announcement): void
    {
        $this->resource = $repository->with(['roles'])->find($announcement);
    }

    protected function prepare(): void
    {
        $roles = $this->resource->roles;

        $this->title(__p('announcement::phrase.edit_announcement'))
            ->action('/admincp/announcement/announcement/' . $this->resource->entityId())
            ->asPut()
            ->setValue([
                'subject'       => $this->resource->subject_var,
                'intro'         => $this->resource->intro_var,
                'text'          => $this->resource->announcementText->text_parsed,
                'is_active'     => $this->resource->is_active,
                'can_be_closed' => $this->resource->can_be_closed,
                'style'         => $this->resource->style->entityId(),
                'start_date'    => Carbon::make($this->resource->start_date)?->toISOString(),
                'roles'         => collect($roles)->pluck('id')->toArray(),
                'country_iso'   => $this->resource->country_iso,
                'gender'        => $this->resource->gender,
            ]);
    }
}
