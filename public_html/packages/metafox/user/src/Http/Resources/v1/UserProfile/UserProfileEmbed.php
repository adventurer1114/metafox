<?php

namespace MetaFox\User\Http\Resources\v1\UserProfile;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\User\Support\Facades\User;

class UserProfileEmbed extends JsonResource
{
    public function toArray($request)
    {
        $relationWithUser = null;

        if ($this->resource->relation_with) {
            $relationWithUser = $this->resource->relationWithUser;

            if (null !== $relationWithUser) {
                $relationWithUser = ResourceGate::asEmbed($this->resource->relationWithUser);
            }
        }

        return [
            'id'            => $this->resource->entityId(),
            'resource_name' => $this->resource->entityType(),
            'module_name'   => 'user',
            'user'          => ResourceGate::asEmbed($this->resource->user),
            'relation'      => [
                'label' => User::getRelationship($this->resource->relation),
                'value' => $this->resource->relation,
            ],
            'relation_with' => $relationWithUser,
            'gender'        => $this->resource->gender,
        ];
    }
}
