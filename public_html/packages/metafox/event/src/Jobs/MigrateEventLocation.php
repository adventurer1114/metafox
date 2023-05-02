<?php

namespace MetaFox\Event\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Event\Repositories\EventRepositoryInterface;

class MigrateEventLocation implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        $apiKey = env('MFOX_GOOGLE_MAP_API_KEY');
        if (empty($apiKey)) {
            return null;
        }

        $events = resolve(EventRepositoryInterface::class)->getMissingLocationEvent();

        if (!$events->count()) {
            return;
        }

        $collections = $events->chunk(50);

        foreach ($collections as $collection) {
            MigrateChunkingEventLocation::dispatch($collection->pluck('id')->toArray());
        }
    }
}
