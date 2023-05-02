<?php

namespace MetaFox\Event\Http\Resources\v1\Event;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItemCollection;
use MetaFox\Event\Http\Resources\v1\Category\CategoryItemCollection;
use MetaFox\Event\Http\Resources\v1\Traits\EventHasExtra;
use MetaFox\Event\Models\Event as Model;
use MetaFox\Event\Support\Facades\EventInvite;
use MetaFox\Event\Support\Facades\EventMembership;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Traits\Http\Resources\HasFeedParam;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class EventDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EventDetail extends JsonResource
{
    use EventHasExtra;
    use HasFeedParam;

    private ?string $inviteCode = null;

    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        return [
            'total_like'                => $this->resource->total_like,
            'total_share'               => $this->resource->total_share,
            'total_view'                => $this->resource->total_view,
            'total_attachment'          => $this->resource->total_attachment,
            'total_member'              => $this->resource->total_member,
            'total_interested'          => $this->resource->total_interested,
            'total_pending_invite'      => $this->resource->total_pending_invites,
            'total_pending_host_invite' => $this->resource->total_pending_host_invites,
            'total_host'                => $this->resource->total_host,
        ];
    }

    public function setInviteCode(string $inviteCode): self
    {
        $this->inviteCode = $inviteCode;

        return $this;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request                 $request
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function toArray($request)
    {
        $context           = user();
        $pendingInvite     = EventInvite::getAvailableInvite($this->resource, $context, $this->inviteCode);
        $pendingHostInvite = EventInvite::getPendingHostInvite($this->resource, $context, $this->inviteCode);

        $owner = ResourceGate::asDetail($this->resource->owner);
        $data  = [
            'id'                => $this->resource->entityId(),
            'module_name'       => $this->resource->entityType(),
            'resource_name'     => $this->resource->entityType(),
            'title'             => $this->resource->name,
            'privacy'           => $this->resource->privacy,
            'description'       => $this->resource->getDescription(),
            'view_id'           => $this->resource->view_id,
            'start_time'        => $this->resource->start_time,
            'end_time'          => $this->resource->end_time,
            'image'             => $this->resource->images,
            'event_url'         => $this->resource->event_url,
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
            'pending_mode'      => $this->resource->isPendingMode(),
            'is_online'         => $this->resource->is_online,
            'is_approved'       => $this->resource->is_approved,
            'is_pending'        => !$this->resource->is_approved,
            'is_sponsor'        => $this->resource->is_sponsor,
            'is_featured'       => $this->resource->is_featured,
            'is_sponsored_feed' => $this->resource->sponsor_in_feed,
            'is_saved'          => PolicyGate::check(
                $this->resource->entityType(),
                'isSavedItem',
                [$context, $this->resource]
            ),
            'is_ended'            => $this->resource->isEnded(),
            'is_host'             => $this->resource->isModerator($context),
            'status'              => $this->resource->getStatus(),
            'rsvp'                => EventMembership::getMembership($this->resource, $context),
            'user'                => new UserEntityDetail($this->resource->userEntity),
            'owner'               => $owner,
            'owner_type_name'     => __p("{$this->resource->ownerType()}::phrase.{$this->resource->ownerType()}"),
            'attachments'         => new AttachmentItemCollection($this->resource->attachments),
            'categories'          => new CategoryItemCollection($this->resource->activeCategories),
            'location'            => $this->resource->toLocationObject(),
            'link'                => $this->resource->toLink(),
            'url'                 => $this->resource->toUrl(),
            'statistic'           => $this->getStatistic(),
            'extra'               => $this->getEventExtra(),
            'feed_param'          => $this->getFeedParams(),
            'pending_invite'      => $pendingInvite ? ResourceGate::asResource($pendingInvite, 'item', false) : null,
            'pending_host_invite' => $pendingHostInvite ? ResourceGate::asResource(
                $pendingHostInvite,
                'item',
                false
            ) : null,
            'parent_id'      => 0,
            'privacy_detail' => $this->getPrivacyDetail(),
        ];

        if ($this->resource->owner instanceof HasPrivacyMember) {
            $data['parent_id'] = $this->resource->ownerId();
        }

        return $data;
    }

    protected function getPrivacyDetail(): ?array
    {
        return app('events')->dispatch(
            'activity.get_privacy_detail_on_owner',
            [user(), $this->resource],
            true
        );
    }
}
