<?php

namespace MetaFox\User\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

class CleanUpDeletedUserJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function uniqueId(): string
    {
        return __CLASS__;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $period  = 0; //@todo: Should this value be converted to a setting?!
        $service = resolve(UserRepositoryInterface::class);

        $service->cleanUpDeletedUser($period);
    }
}
