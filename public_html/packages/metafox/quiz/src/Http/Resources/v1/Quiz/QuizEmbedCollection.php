<?php

namespace MetaFox\Quiz\Http\Resources\v1\Quiz;

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

class QuizEmbedCollection extends ResourceCollection
{
    public $collects = QuizEmbed::class;
}
