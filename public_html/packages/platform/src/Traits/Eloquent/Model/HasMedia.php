<?php

namespace MetaFox\Platform\Traits\Eloquent\Model;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Media;

/**
 * @mixin Model
 * @mixin Media
 * @property int $in_process
 */
trait HasMedia
{
    public function getIsProcessingAttribute(): bool
    {
        return array_key_exists('in_process', $this->attributes) ? (bool) $this->in_process : true;
    }
}
