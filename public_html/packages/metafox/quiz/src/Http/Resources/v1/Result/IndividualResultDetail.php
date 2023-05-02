<?php

namespace MetaFox\Quiz\Http\Resources\v1\Result;

use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

class IndividualResultDetail extends ResultDetail
{
    public function toArray($request): array
    {
        $response = parent::toArray($request);

        $response['user']          = new UserEntityDetail($this->resource->userEntity);
        $response['module_name']   = 'quiz';
        $response['resource_name'] = 'quiz_view_individual';
        $response['id']            = $this->resource->quiz_id;

        return $response;
    }
}
