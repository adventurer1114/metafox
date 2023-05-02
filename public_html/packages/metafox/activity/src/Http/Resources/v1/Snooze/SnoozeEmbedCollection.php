<?php

namespace MetaFox\Activity\Http\Resources\v1\Snooze;

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

/**
 * Class SnoozeEmbedCollection.
 * @codeCoverageIgnore - not used yet
 */
class SnoozeEmbedCollection extends ResourceCollection
{
    public $collects = SnoozeEmbed::class;
}
