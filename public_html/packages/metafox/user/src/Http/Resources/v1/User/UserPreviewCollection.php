<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserPreviewCollection extends ResourceCollection
{
    public $collects = UserPreview::class;
}
