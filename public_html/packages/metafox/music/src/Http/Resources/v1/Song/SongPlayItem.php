<?php

namespace MetaFox\Music\Http\Resources\v1\Song;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Music\Models\Song;
use MetaFox\Music\Support\Browse\Traits\Song\ExtraTrait;
use MetaFox\Music\Support\Browse\Traits\Song\StatisticTrait;
use MetaFox\Platform\Facades\PolicyGate;

/**
 * @property Song $resource
 */
class SongPlayItem extends JsonResource
{
    use StatisticTrait;
    use ExtraTrait;

    public function toArray($request)
    {
        $context = user();

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'music',
            'resource_name' => $this->resource->entityType(),
            'name'          => $this->resource->name,
            'duration'      => $this->resource->duration,
            'album_id'      => $this->resource->album_id,
            'image'         => $this->resource->images,
            'destination'   => $this->resource->link_media_file,
            'is_saved'      => PolicyGate::check($this->resource->entityType(), 'isSavedItem', [$context, $this->resource]),
            'statistic'     => $this->getStatistic(),
            'extra'         => $this->getExtra(),
            'creation_date' => $this->resource->created_at,
        ];
    }
}
