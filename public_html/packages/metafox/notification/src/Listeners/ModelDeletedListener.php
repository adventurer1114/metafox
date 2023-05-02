<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Notification\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Notification\Repositories\NotificationRepositoryInterface;
use MetaFox\Platform\Contracts\IsNotifyInterface;
use MetaFox\Platform\Notifications\Notification;

/**
 * Class ModelDeletedListener.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class ModelDeletedListener
{
    private NotificationRepositoryInterface $repository;

    public function __construct(NotificationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Model $model): void
    {
        $this->handleToNotification($model);

        $this->handleToApproveNotification($model);
    }

    private function handleToNotification(Model $model)
    {
        if (!$model instanceof IsNotifyInterface) {
            return;
        }

        $this->handleDeleteNotification($model, $model->toNotification());
    }

    private function handleToApproveNotification(Model $model)
    {
        if (!method_exists($model, 'toApprovedNotification')) {
            return;
        }

        $this->handleDeleteNotification($model, $model->toApprovedNotification());
    }

    private function handleDeleteNotification(Model $model, $response)
    {
        if (!is_array($response)) {
            return;
        }

        [, $notification] = $response;

        if ($notification instanceof Notification) {
            $this->repository->deleteNotificationByItem($notification->getType(), $model->entityId(), $model->entityType());
        }
    }
}
