<?php

namespace MetaFox\Announcement\Observers;

use MetaFox\Announcement\Models\AnnouncementText;

/**
 * Class AnnouncementTextObserver.
 * @ignore
 * @codeCoverageIgnore
 */
class AnnouncementTextObserver
{
    /**
     * @param AnnouncementText $model
     */
    public function creating(AnnouncementText $model): void
    {
        $model->text_parsed = $model->text;
    }
}

// end stub
