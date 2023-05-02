<?php

namespace MetaFox\Friend\Http\Requests\v1\Friend;

class MentionRequest extends IndexRequest
{
    protected function getDefaultLimit(): int
    {
        return 5;
    }
}
