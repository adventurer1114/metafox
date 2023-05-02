<?php

namespace MetaFox\Activity\Listeners;

use MetaFox\Activity\Jobs\MigrateFeedContent;

class ImporterCompleted
{
    public function handle(): void
    {
        MigrateFeedContent::dispatch(true);
    }
}
