<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Group\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Group\Contracts\UserDataInterface;
use MetaFox\Group\Repositories\AnnouncementRepositoryInterface;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\HasFeed;
use MetaFox\Platform\Contracts\User;

/**
 * Class ModelDeletedListener.
 * @ignore
 */
class ModelDeletedListener
{
    public function __construct(
        protected UserDataInterface $userDataRepository,
    ) {
    }
    /**
     * @param Model $model
     */
    public function handle(Model $model): void
    {
        if ($model instanceof User) {
            $this->userDataRepository->deleteAllBelongToUser($model);
        }
    }
}
