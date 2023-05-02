<?php

namespace MetaFox\Group\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use MetaFox\Group\Models\GroupInviteCode;
use MetaFox\Group\Repositories\GroupInviteCodeRepositoryInterface;

/**
 * stub: packages/jobs/job-queued.stub.
 */
class UpdateStatusCodeInviteJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GroupInviteCodeRepositoryInterface $codeRepository)
    {
        $inviteCode = $codeRepository
            ->getModel()
            ->newModelQuery()
            ->where('status', GroupInviteCode::STATUS_ACTIVE)
            ->where(function (Builder $builder) {
                $builder->whereNull('expired_at')
                    ->orWhere('expired_at', '<=', Carbon::now()->toDateString());
            });
        $inviteCode->update(['status' => GroupInviteCode::STATUS_INACTIVE]);
    }
}
