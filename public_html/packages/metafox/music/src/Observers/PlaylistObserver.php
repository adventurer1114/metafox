<?php

namespace MetaFox\Music\Observers;

use MetaFox\Music\Models\Playlist;

class PlaylistObserver
{
    public function deleted(Playlist $playlist): void
    {
        if ($playlist->image_file_id) {
            upload()->rollUp($playlist->image_file_id);
        }

        $playlist->songs()->sync([]);
    }
}
