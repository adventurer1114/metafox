<?php

namespace MetaFox\Announcement\Http\Resources\v1\Announcement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use MetaFox\Announcement\Models\Announcement as Model;
use MetaFox\Announcement\Repositories\AnnouncementViewRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Announcement\Http\Resources\v1\Traits\AnnouncementHasExtra;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Http\Resources\HasFeedParam;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Detail
|--------------------------------------------------------------------------
|
| @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview
| @link /app/Console/Commands/stubs/module/resources/detail.stub
|
*/

/**
 * Class AnnouncementDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AnnouncementDetail extends JsonResource
{
    use AnnouncementHasExtra;
    use HasStatistic;
    use IsLikedTrait;
    use HasFeedParam;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $context = user();

        $roleText = __p('announcement::phrase.all_roles');
        $roles    = collect($this->resource->roles);
        if ($roles->isNotEmpty()) {
            $roleText = $roles->pluck('name')->implode(', ');
        }

        return [
            'id'                => $this->resource->entityId(),
            'module_name'       => $this->resource->entityType(),
            'resource_name'     => $this->resource->entityType(),
            'title'             => $this->resource->subject_var,
            'description'       => $this->resource->intro_var,
            'text'              => $this->resource->announcementText->text,
            'text_parsed'       => $this->resource->announcementText->text_parsed,
            'style'             => $this->resource->style->name,
            'roles'             => $roleText,
            'icon_image'        => $this->resource->style->icon_image,
            'icon_font'         => $this->resource->style->icon_font,
            'can_be_closed'     => $this->resource->can_be_closed,
            'is_liked'          => $this->isLike($context, $this->resource),
            'feed_param'        => $this->getFeedParams(),
            'show_in_dashboard' => $this->resource->show_in_dashboard,
            'start_date'        => Carbon::make($this->resource->start_date)?->toISOString(),
            'creation_date'     => $this->resource->created_at,
            'moderation_date'   => $this->resource->updated_at,
            'is_active'         => $this->resource->is_active,
            'is_read'           => $this->isRead($context),
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'link'              => $this->resource->toLink(),
            'url'               => $this->resource->toUrl(),
            'privacy'           => MetaFoxPrivacy::EVERYONE,
            'extra'             => $this->getAnnouncementExtra(),
            'statistic'         => $this->getStatistic(),
        ];
    }

    protected function isRead(User $context): bool
    {
        return resolve(AnnouncementViewRepositoryInterface::class)
            ->checkViewAnnouncement($context->entityId(), $this->resource->entityId());
    }
}
