<?php

namespace MetaFox\BackgroundStatus\Http\Resources\v1\BgsBackground;

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

class BgsBackgroundEmbedCollection extends ResourceCollection
{
    public $collects = BgsBackgroundEmbed::class;
}
