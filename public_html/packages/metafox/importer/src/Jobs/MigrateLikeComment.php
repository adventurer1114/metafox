<?php

namespace MetaFox\Importer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Photo\Models\PhotoGroup;

class MigrateLikeComment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $photoGroups = PhotoGroup::query()
            ->where('total_item', 1)
            ->orderBy('id')
            ->get();

        if (!$photoGroups->count()) {
            return;
        }

        $collections = $photoGroups->chunk(100);

        foreach ($collections as $collection) {
            MigrateChunkingLikeComment::dispatch($collection->pluck('id')->toArray());
        }
    }
}
