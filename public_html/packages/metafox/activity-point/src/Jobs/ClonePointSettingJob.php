<?php

namespace MetaFox\ActivityPoint\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\ActivityPoint\Repositories\PointSettingRepositoryInterface;
use MetaFox\Platform\Contracts\Entity;

class ClonePointSettingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var bool
     */
    public bool $deleteWhenMissingModels = true;

    public function __construct(protected Entity $role)
    {
    }

    public function handle()
    {
        resolve(PointSettingRepositoryInterface::class)->clonePointSettings($this->role->entityId(), $this->role->parent_id);
    }
}
