<?php

namespace MetaFox\Event\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Support\Facades\Event as EventSupport;

class MigrateChunkingEventLocation implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected array $eventIds = [])
    {
    }

    public function handle()
    {
        $events = Event::query()
            ->whereIn('id', $this->eventIds)
            ->get();

        if (!$events->count()) {
            return;
        }

        foreach ($events as $event) {
            if (!$event instanceof Event) {
                continue;
            }

            $location = EventSupport::createLocationWithName($event->location_name);

            $event->update([
                'location_name'      => $location['location_name'] ?? null,
                'location_latitude'  => $location['location_latitude'] ?? null,
                'location_longitude' => $location['location_longitude'] ?? null,
            ]);
        }
    }
}
