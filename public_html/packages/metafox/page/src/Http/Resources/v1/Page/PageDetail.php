<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Page\Http\Resources\v1\PageCategory\PageCategoryEmbed;
use MetaFox\Page\Http\Resources\v1\Traits\IsUserInvited;
use MetaFox\Page\Http\Resources\v1\Traits\PageHasExtra;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Page\Models\PageInvite;
use MetaFox\Page\Repositories\PageInviteRepositoryInterface;
use MetaFox\Page\Support\Facade\Page as PageFacade;
use MetaFox\Page\Support\Facade\PageMembership;
use MetaFox\Platform\Contracts\ResourceText;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Support\Facades\User;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class PageDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PageDetail extends JsonResource
{
    use PageHasExtra;
    use HasStatistic;
    use IsUserInvited;

    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        return [
            'total_like'  => $this->resource->total_member,
            'total_admin' => $this->resource->total_admin,
        ];
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string,           mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $context = user();

        $pageText    = $this->resource->pageText;
        $text        = $description = '';
        $landingPage = $this->resource->landing_page ?? 'home';

        if ($pageText instanceof ResourceText) {
            $description = parse_output()->getDescription($pageText->text_parsed);
            $text        = parse_output()->parse($pageText->text_parsed);
        }

        $pendingInvite = null;
        if ($this->resource->isMember($context)) {
            $pendingInvite = resolve(PageInviteRepositoryInterface::class)
                ->getPendingInvite($this->resource->entityId(), $context, PageInvite::INVITE_ADMIN);
        }

        return [
            'id'                   => $this->resource->entityId(),
            'module_name'          => $this->resource->entityType(),
            'resource_name'        => $this->resource->entityType(),
            'title'                => $this->resource->name,
            'privacy'              => $this->resource->privacy,
            'category'             => new PageCategoryEmbed($this->resource->category),
            'user'                 => new UserEntityDetail($this->resource->userEntity),
            'text'                 => $text,
            'description'          => $description,
            'external_link'        => $this->resource->external_link,
            'view_id'              => $this->resource->is_approved ? 0 : 1,
            'is_liked'             => $this->resource->isMember($context),
            'is_admin'             => $this->resource->isAdmin($context),
            'is_owner'             => $this->resource->isUser($context),
            'is_member'            => $this->resource->isMember($context),
            'is_pending'           => !$this->resource->is_approved,
            'is_invited'           => $this->isUserInvited($context),
            'is_featured'          => (bool) $this->resource->is_featured,
            'is_sponsor'           => (bool) $this->resource->is_sponsor,
            'is_following'         => PageFacade::isFollowing($context, $this->resource),
            'membership'           => PageMembership::getMembership($this->resource, $context),
            'image'                => $this->resource->avatars, //@todo: remove later if not used anymore
            'image_id'             => $this->resource->getAvatarId(), //@todo: remove later if not used anymore
            'avatar'               => $this->resource->avatars,
            'avatar_id'            => $this->resource->getAvatarId(),
            'cover'                => $this->resource->covers,
            'cover_photo_position' => $this->resource->cover_photo_position,
            'cover_photo_id'       => $this->resource->getCoverId(),
            'latitude'             => $this->resource->location_latitude,
            'longitude'            => $this->resource->location_longitude,
            'location_name'        => $this->resource->location_name,
            'item_type'            => $this->resource->entityType(),
            'type_name'            => '',
            'short_name'           => User::getShortName($this->resource->name),
            'defaultActiveTabMenu' => $landingPage,
            'summary'              => $this->resource->summary,
            'link'                 => $this->resource->toLink(),
            'url'                  => $this->resource->toUrl(),
            'creation_date'        => $this->resource->created_at,
            'modification_date'    => $this->resource->updated_at,
            'statistic'            => $this->getStatistic(),
            'extra'                => $this->getExtra(),
            'profile_settings'     => UserPrivacy::hasAccessProfileSettings($context, $this->resource),
            'invite'               => $pendingInvite ? ResourceGate::asResource($pendingInvite, 'item', false) : null,
            'cover_resource'       => $this->getCoverResources(),
            'avatar_resource'      => $this->getAvatarResources(),
            'privacy_detail'       => $this->getPrivacyDetail(),
        ];
    }

    protected function getPrivacyDetail(): ?array
    {
        return app('events')->dispatch(
            'activity.get_privacy_detail_on_owner',
            [user(), $this->resource],
            true
        );
    }

    protected function getCoverResources(): ?JsonResource
    {
        return !empty($this->resource->cover)
            ? ResourceGate::asDetail($this->resource->cover()->first())
            : null;
    }

    protected function getAvatarResources(): ?JsonResource
    {
        return !empty($this->resource->avatar)
            ? ResourceGate::asDetail($this->resource->avatar()->first())
            : null;
    }
}
