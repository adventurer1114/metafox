<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\User\Models\User as Modal;

/**
 * @property Modal $model
 */
class AdminLogged extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request); // TODO: Change the autogenerated stub
    }
}
