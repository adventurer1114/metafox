<?php

namespace MetaFox\Hashtag\Observers;

use Illuminate\Support\Str;
use MetaFox\Hashtag\Models\Tag;

/**
 * Class TagObserver.
 */
class TagObserver
{
    public function creating(Tag $tag): void
    {
        $tag->tag_url = Str::lower(Str::slug($tag->text));
    }
}
