<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Group\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Group\Repositories\AnnouncementRepositoryInterface;
use MetaFox\Platform\Contracts\Content;

/**
 * Class ModelDeletedListener.
 * @ignore
 */
class AnnouncementDeletedListener
{
    public function __construct(
        protected AnnouncementRepositoryInterface $announcementRepository
    ) {
    }
    /**
     * @param Model $model
     */
    public function handle(Model $model): void
    {
        if (!$model instanceof Content) {
            return;
        }

        $this->announcementRepository->deleteByItem($model->entityId(), $model->entityType());
    }
}
