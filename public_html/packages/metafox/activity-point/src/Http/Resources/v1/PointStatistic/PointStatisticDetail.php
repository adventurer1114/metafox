<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointStatistic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\ActivityPoint\Models\PointStatistic as Model;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/detail.stub
*/

/**
 * Class PointStatisticDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @mixin Model
 */
class PointStatisticDetail extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->entityId(),
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'module_name'   => 'activitypoint',
            'resource_name' => $this->resource->entityType(),
            'items'         => $this->getStatisticItems($this->resource),
        ];
    }

    /**
     * @param  Model             $resource
     * @return array<int, mixed>
     */
    protected function getStatisticItems(Model $resource): array
    {
        return [
            // Total points
            [
                'label' => __p('activitypoint::phrase.type_all_label'),
                'hint'  => __p('activitypoint::phrase.total_point_description'),
                'value' => $resource->current_points,
            ],
            // Total earned
            [
                'label' => __p('activitypoint::phrase.type_earned_label'),
                'hint'  => __p('activitypoint::phrase.total_earned_description'),
                'value' => $resource->total_earned,
            ],
            // Total bought
            [
                'label' => __p('activitypoint::phrase.type_bought_label'),
                'hint'  => __p('activitypoint::phrase.total_bought_description'),
                'value' => $resource->total_bought,
            ],
            // Total sent
            [
                'label' => __p('activitypoint::phrase.type_sent_label'),
                'hint'  => __p('activitypoint::phrase.total_sent_description'),
                'value' => $resource->total_sent,
            ],
            // Total spent
            [
                'label' => __p('activitypoint::phrase.type_spent_label'),
                'hint'  => __p('activitypoint::phrase.total_spent_description'),
                'value' => $resource->total_spent,
            ],
            // Total received
            [
                'label' => __p('activitypoint::phrase.type_received_label'),
                'hint'  => __p('activitypoint::phrase.total_received_description'),
                'value' => $resource->total_received,
            ],
            // Total retrieved
            [
                'label' => __p('activitypoint::phrase.type_retrieved_label'),
                'hint'  => __p('activitypoint::phrase.total_retrieved_description'),
                'value' => $resource->total_retrieved,
            ],
        ];
    }
}
