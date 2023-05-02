<?php

namespace MetaFox\Event\Http\Resources\v1\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItemCollection;
use MetaFox\Event\Http\Resources\v1\Category\CategoryItemCollection;
use MetaFox\Event\Http\Resources\v1\Traits\EventHasExtra;
use MetaFox\Event\Models\Event as Model;
use MetaFox\Event\Support\Facades\EventMembership;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Embed
|--------------------------------------------------------------------------
|
| Resource embed is used when you want attach this resource as embed content of
| activity feed, notification, ....
| @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview
| @link /app/Console/Commands/stubs/module/resources/detail.stub
*/

/**
 * Class EventEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EventEmbed extends JsonResource
{
    use EventHasExtra;

    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        return [
            'total_like'       => $this->resource->total_like,
            'total_share'      => $this->resource->total_share,
            'total_view'       => $this->resource->total_view,
            'total_attachment' => $this->resource->total_attachment,
            'total_member'     => $this->resource->total_member,
            'total_interested' => $this->resource->total_interested,
        ];
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $context = user();

        return [
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
            'is_ended'    => $this->resource->isEnded(),
            'status'      => $this->resource->getStatus(),
            'rsvp'        => EventMembership::getMembership($this->resource, $context),
            'user'        => new UserEntityDetail($this->resource->userEntity),
            'attachments' => new AttachmentItemCollection($this->resource->attachments),
            'categories'  => new CategoryItemCollection($this->resource->activeCategories),
            'location'    => $this->resource->toLocationObject(),
            'link'        => $this->resource->toLink(),
            'url'         => $this->resource->toUrl(),
            'statistic'   => $this->getStatistic(),
            'extra'       => $this->getEventExtra(),
        ];
    }
}
