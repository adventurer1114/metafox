<?php

namespace MetaFox\Music\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Music\Models\Album;

class MigrateAlbumGenre implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $albums = Album::query()
            ->select('music_albums.*')
            ->join('importer_entries', function (JoinClause $joinClause) {
                $joinClause->on('importer_entries.resource_id', '=', 'music_albums.id')
                    ->where('importer_entries.resource_type', 'music_album');
            })
            ->where('total_track', '>=', 1)
            ->orderBy('id')
            ->get();

        if (!$albums->count()) {
            return;
        }

        $collections = $albums->chunk(100);

        foreach ($collections as $collection) {
            MigrateChunkingAlbumGenre::dispatch($collection->pluck('id')->toArray());
        }
    }
}
