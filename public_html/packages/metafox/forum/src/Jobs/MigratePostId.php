<?php

namespace MetaFox\Forum\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Forum\Models\ForumThread;

class MigratePostId implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $threads = ForumThread::query()
            ->where([
                'is_approved' => 1,
            ])
            ->orderBy('id')
            ->get();

        if (!$threads->count()) {
            return;
        }

        $collections = $threads->chunk(100);

        foreach ($collections as $collection) {
            MigrateChunkingPostId::dispatch($collection->pluck('id')->toArray());
        }
    }
}
