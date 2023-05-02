<?php

namespace MetaFox\Comment\Traits;

use Illuminate\Support\Str;
use MetaFox\Comment\Models\Comment;
use MetaFox\Hashtag\Models\Tag;
use MetaFox\Platform\Contracts\HasHashTag;

/**
 * @property Comment $resource
 */
trait HasTransformContent
{
    /**
     * @return string|null
     */
    public function getTransformContent(): ?string
    {
        $content = $this->resource->text_parsed;

        if ($this->resource instanceof HasHashTag) {
            $this->resource->tagData->each(function (Tag $tag) use (&$content) {
                $hashtag = '#' . $tag->text;
                $link    = parse_output()->buildHashtagLink($hashtag, $tag->tag_url);
                $content = Str::of($content)->replaceFirst($hashtag, $link);
            });
        }

        if ($content !== null) {
            app('events')->dispatch('core.parse_content', [$this->resource, &$content, $this->resource->entityType()]);
        }

        return $content;
    }
}
