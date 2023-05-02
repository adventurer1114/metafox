<?php

namespace MetaFox\Platform\Traits\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;

/**
 * Trait HasStatistic.
 * @property Model $resource
 */
trait HasStatistic
{
    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        return [
            'total_view'    => $this->resource instanceof HasTotalView ? $this->resource->total_view : 0,
            'total_like'    => $this->resource instanceof HasTotalLike ? $this->resource->total_like : 0,
            'total_comment' => $this->resource instanceof HasTotalComment ? $this->resource->total_comment : 0,
            'total_reply'   => $this->resource instanceof HasTotalCommentWithReply ? $this->resource->total_reply : 0,
            'total_share'   => $this->resource instanceof HasTotalShare ? $this->resource->total_share : 0,
        ];
    }
}
