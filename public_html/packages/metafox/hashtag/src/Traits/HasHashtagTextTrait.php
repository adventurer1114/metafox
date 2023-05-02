<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Hashtag\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MetaFox\Hashtag\Models\Tag;
use MetaFox\Platform\Contracts\HasHashTag;

/**
 * Trait HasHashtagTextTrait.
 * @property Model $resource
 */
trait HasHashtagTextTrait
{
    /**
     * @param  string $content
     * @return string
     */
    public function getTransformContent(string $content): string
    {
        if ($this->resource instanceof HasHashTag) {
            $content = $this->parseHashtags($content);
        }

        if ($content !== null) {
            app('events')->dispatch('core.parse_content', [$this->resource, &$content, $this->resource->entityType()]);
        }

        return $content;
    }

    protected function parseHashtags(?string $content): ?string
    {
        if (null === $content) {
            return null;
        }

        $contentHashtags = $this->buildContentTags($content);

        $resourceHashtags = $this->buildResourceTags();

        foreach ($contentHashtags as $temp => $hashtag) {
            $tagUrl = Arr::get($resourceHashtags, $hashtag);

            if (!is_string($tagUrl)) {
                continue;
            }

            $search = '#' . $temp;

            $link = parse_output()->buildHashtagLink($search, $tagUrl);

            $content = Str::of($content)->replace($search, $link);
        }

        if (is_object($content)) {
            return $content->toString();
        }

        return $content;
    }

    protected function buildContentTags(?string $content): array
    {
        if (null === $content) {
            return [];
        }

        $tags = parse_output()->getHashtags($content);

        if (!count($tags)) {
            return [];
        }

        $mapping = [];

        foreach ($tags as $tag) {
            $mapping[$tag] = Str::lower($tag);
        }

        return $mapping;
    }

    protected function buildResourceTags(): array
    {
        $tags = $this->resource->tagData;

        if (!count($tags)) {
            return [];
        }

        $mapping = [];

        foreach ($tags as $tag) {
            Arr::set($mapping, $tag->text, $tag->tag_url);
        }

        return $mapping;
    }
}
