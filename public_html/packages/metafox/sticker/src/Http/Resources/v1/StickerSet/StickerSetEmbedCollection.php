<?php

namespace MetaFox\Sticker\Http\Resources\v1\StickerSet;

use Illuminate\Http\Resources\Json\ResourceCollection;

/*
|--------------------------------------------------------------------------
| Resource Collection
|--------------------------------------------------------------------------
|
| @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview
| @link /app/Console/Commands/stubs/module/resources/detail.stub
|
*/

class StickerSetEmbedCollection extends ResourceCollection
{
    public $collects = StickerSetEmbed::class;
}
