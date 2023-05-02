<?php

namespace MetaFox\Announcement\Http\Resources\v1\Announcement;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use MetaFox\Announcement\Models\Announcement as Model;
use MetaFox\Announcement\Repositories\AnnouncementViewRepositoryInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class AnnouncementItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AnnouncementItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $context  = user();
        $roleText = __p('announcement::phrase.all_roles');
        $roles    = collect($this->resource->roles);
        if ($roles->isNotEmpty()) {
            $roleText = $roles->pluck('name')->implode(', ');
        }

        return [
            'id'              => $this->resource->entityId(),
            'module_name'     => $this->resource->entityType(),
            'resource_name'   => $this->resource->entityType(),
            'title'           => $this->resource->subject_var,
            'description'     => $this->resource->intro_var,
            'style'           => $this->resource->style->name,
            'roles'           => $roleText,
            'icon_image'      => $this->resource->style->icon_image,
            'icon_font'       => $this->resource->style->icon_font,
            'start_date'      => Carbon::make($this->resource->start_date)?->toISOString(),
            'creation_date'   => $this->resource->created_at,
            'moderation_date' => $this->resource->updated_at,
            'is_active'       => $this->resource->is_active,
            'is_read'         => $this->isRead($context),
            'can_be_closed'   => $this->resource->can_be_closed,
            'link'            => $this->resource->toLink(),
            'url'             => $this->resource->toUrl(),
            'statistic'       => $this->getStatistic(),
            'extra'           => $this->getExtra(),
        ];
    }

    /**
     * @return array<string, int>
     */
    public function getStatistic(): array
    {
        return [
            'total_view' => $this->resource->total_view,
        ];
    }

    /**
     * @return array<string, int>
     */
    public function getExtra(): array
    {
        return [
            'can_close' => true,
        ];
    }

    protected function isRead(User $context): bool
    {
        return resolve(AnnouncementViewRepositoryInterface::class)
            ->checkViewAnnouncement($context->entityId(), $this->resource->entityId());
    }
}
