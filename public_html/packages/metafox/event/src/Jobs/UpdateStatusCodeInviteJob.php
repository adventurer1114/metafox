<?php

namespace MetaFox\Event\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Event\Models\InviteCode;
use MetaFox\Event\Repositories\InviteCodeRepositoryInterface;


/**
 * stub: packages/jobs/job-queued.stub
 */
class UpdateStatusCodeInviteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(InviteCodeRepositoryInterface $codeRepository)
    {
        $inviteCode = $codeRepository
            ->getModel()
            ->newModelQuery()
            ->where('status', InviteCode::STATUS_ACTIVE)
            ->whereDate('expired_at', '<=', Carbon::now());
        $inviteCode->update(['status' => InviteCode::STATUS_INACTIVE]);
    }
}
