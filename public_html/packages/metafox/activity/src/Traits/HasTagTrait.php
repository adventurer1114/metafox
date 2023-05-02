<?php

namespace MetaFox\Activity\Traits;

use MetaFox\Hashtag\Traits\HasHashtagTextTrait;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasHashTag;

trait HasTagTrait
{
    use HasHashtagTextTrait;

    /**
     * @return string|null
     */
    public function getParsedContent(): ?string
    {
        $content = $this->resource->content;

        if ($this->resource instanceof HasHashTag) {
            $content = $this->parseHashtags($content);
        }

        $content = parse_output()->parse($content);

        $item = $this->resource->item;

        if ($item instanceof Entity) {
            app('events')->dispatch('core.parse_content', [$item, &$content]);
        }

        return $content;
    }
}
