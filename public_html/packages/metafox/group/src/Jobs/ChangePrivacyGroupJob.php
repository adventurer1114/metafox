<?php

namespace MetaFox\Group\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Group\Models\GroupChangePrivacy;
use MetaFox\Group\Repositories\GroupChangePrivacyRepositoryInterface;

/**
 * stub: packages/jobs/job-queued.stub.
 */
class ChangePrivacyGroupJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
    public function handle(GroupChangePrivacyRepositoryInterface $changePrivacyRepository)
    {
        $now   = Carbon::now();
        $model = new GroupChangePrivacy();
        $items = $model->newQuery()->where([
            'is_active' => GroupChangePrivacy::IS_ACTIVE,
        ])->whereDate('expired_at', '<=', $now)->get();

        foreach ($items as $item) {
            $changePrivacyRepository->sentNotificationWhenSuccess($item->entityId());
            $changePrivacyRepository->updatePrivacyGroup($item->group, $item->privacy_type);
        }

        $items->update([
            'is_active' => GroupChangePrivacy::IS_NOT_ACTIVE,
        ]);
    }
}
