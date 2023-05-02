<?php

namespace MetaFox\Music\Listeners;

use MetaFox\Music\Jobs\MigrateAlbumGenre;

class ImporterCompleted
{
    public function handle(): void
    {
        MigrateAlbumGenre::dispatch();
    }
}
