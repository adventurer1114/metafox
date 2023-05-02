<?php

namespace MetaFox\Group\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use MetaFox\Group\Models\Mute;
use MetaFox\Group\Repositories\MuteRepositoryInterface;

/**
 * Class UnMuteInGroupJob.
 * @ignore
 */
class UnmuteInGroupJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * DeleteCategoryJob constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(MuteRepositoryInterface $muteRepository)
    {
        $member = $muteRepository->getModel()->newModelQuery()
            ->where('status', Mute::STATUS_MUTED)
            ->where(function (Builder $builder) {
                $builder->whereNull('expired_at')
                    ->orWhere('expired_at', '<=', Carbon::now()->toDateString());
            });
        $member->delete();
    }
}
