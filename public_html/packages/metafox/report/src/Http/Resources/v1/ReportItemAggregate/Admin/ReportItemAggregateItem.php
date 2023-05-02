<?php

namespace MetaFox\Report\Http\Resources\v1\ReportItemAggregate\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Report\Models\ReportItemAggregate as Model;
use MetaFox\Platform\Contracts\User;

/**
 * Class ReportItemAggregateItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class ReportItemAggregateItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $item = $this->resource->item;
        $user = $this->last_user;

        $userName = __p('core::phrase.deleted_user');
        if ($user instanceof User) {
            $userName = $user->full_name;
        }

        return [
            'id'                => $this->resource->entityId(),
            'item_title'        => $item?->toTitle() ?? __p('core::phrase.deleted_item'),
            'item_url'          => $item?->toUrl(),
            'report_detail_url' => $this->getItemDetailLink($item),
            'user'              => $user,
            'last_user_name'    => $userName,
            'last_user_url'     => $user?->toUrl(),
            'total_reports'     => $this->resource->total_reports,
            'created_at'        => $this->resource->created_at,
        ];
    }

    protected function getItemDetailLink(?Entity $item): ?string
    {
        if (!$item instanceof Entity) {
            return null;
        }

        return sprintf('/admincp/report/aggregate/%s/item/browse', $this->resource->entityId());
    }
}
