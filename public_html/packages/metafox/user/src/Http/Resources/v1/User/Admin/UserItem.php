<?php

namespace MetaFox\User\Http\Resources\v1\User\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\ResourcePermission;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Models\User;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Models\UserActivity;
use MetaFox\User\Policies\UserPolicy;
use MetaFox\User\Support\Facades\User as UserFacade;

/**
 * Class UserItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $ipAddress    = '';
        $lastActivity = '';

        $userActivity = $this->resource->userActivity;

        if ($userActivity instanceof UserActivity) {
            $ipAddress    = $userActivity->last_ip_address;
            $lastActivity = $userActivity->last_activity;
        }

        return [
            'id'              => $this->resource->entityId(),
            'module_name'     => 'user',
            'resource_name'   => $this->resource->entityType(),
            'full_name'       => $this->resource->full_name,
            'user_name'       => $this->resource->user_name,
            'avatar'          => $this->resource->profile->avatars,
            'user'            => new UserEntityDetail($this->resource->userEntity),
            'role_name'       => $this->resource->transformRole(),
            'email'           => $this->resource->email,
            'user_link'       => $this->resource->userEntity?->toUrl(),
            'created_at'      => Carbon::create($this->resource->created_at)->toDateTimeString(),
            'ip_address'      => $ipAddress,
            'last_activity'   => Carbon::create($lastActivity)->toDateTimeString(),
            'is_approved'     => $this->resource->isApproved(),
            'approve_status'  => $this->resource->approve_status,
            'is_featured'     => $this->resource->is_featured,
            'is_mfa_enabled'  => app('events')->dispatch('user.user_mfa_enabled', [$this->resource], true),
            'is_banned'       => UserFacade::isBan($this->resource->entityId()),
            'is_verify_email' => $this->resource->hasVerifiedEmail(),
            'extra'           => $this->getExtra(),
            'links'           => [
                'editItem' => '/admincp/user/user/edit/' . $this->resource->entityId(),
            ],
        ];
    }

    /**
     * @return array<string, bool>
     */
    private function getExtra()
    {
        $policy = PolicyGate::getPolicyFor(User::class);
        if (!$policy instanceof UserPolicy) {
            abort(400, 'Missing Policy');
        }

        $context = user();

        return [
            ResourcePermission::CAN_EDIT    => $policy->manage($context, $this->resource),
            ResourcePermission::CAN_FEATURE => $policy->feature($context, $this->resource),
        ];
    }
}
