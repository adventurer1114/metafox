<?php

namespace MetaFox\Search\Http\Resources\v1\Search;

use Illuminate\Http\Resources\Json\JsonResource;

class TrendingHashtagItem extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'search',
            'resource_name' => $this->resource->entityType(),
            'text'          => $this->resource->text,
            'tag_url'       => $this->resource->tag_url,
            'tag_hyperlink' => $this->resource->tag_hyperlink,
        ];
    }
}
