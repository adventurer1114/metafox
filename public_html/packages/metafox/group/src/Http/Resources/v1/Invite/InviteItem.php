<?php

namespace MetaFox\Group\Http\Resources\v1\Invite;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Group\Models\Invite as Model;
use MetaFox\User\Http\Resources\v1\User\UserItem;

/**
 * Class InviteItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class InviteItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function toArray($request): array
    {
        $owner              = $this->resource->owner;
        $expiredAt          = $this->resource->expired_at;
        $expiredDay         = null;
        $expiredDescription = null;

        if ($expiredAt !== null) {
            $expiredDay         = Carbon::now()->diffInHours($expiredAt) + 1;
            $expiredDescription = __p(
                'group::phrase.expired_invite_hours',
                [
                    'value' => CarbonInterval::make($expiredDay . 'h')
                        ->locale($owner->preferredLocale())
                        ->cascade()
                        ->forHumans(),
                ]
            );
        }

        return [
            'id'                  => $this->resource->entityId(),
            'module_name'         => 'group',
            'resource_name'       => $this->resource->entityType(),
            'status_id'           => $this->resource->status_id,
            'group_id'            => $this->resource->group_id,
            'invited_member'      => $this->resource->isInviteMember(),
            'invited_admin'       => $this->resource->isInviteAdmin(),
            'invited_moderator'   => $this->resource->isInviteModerator(),
            'invited_link'        => $this->resource->isInviteLink(),
            'user'                => new UserItem($this->resource->user),
            'owner'               => new UserItem($owner),
            'expired_day'         => $expiredDay,
            'expired_description' => $expiredDescription,
        ];
    }
}
