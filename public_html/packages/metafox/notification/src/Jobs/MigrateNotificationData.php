<?php

namespace MetaFox\Notification\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Notification\Models\Notification;

class MigrateNotificationData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $notifications = Notification::query()
            ->whereNull('data')
            ->orderBy('id')
            ->get();

        if (!$notifications->count()) {
            return;
        }

        $collections = $notifications->chunk(100);

        foreach ($collections as $collection) {
            MigrateChunkingNotificationData::dispatch($collection->pluck('id')->toArray());
        }
    }
}
