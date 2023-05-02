<?php

namespace MetaFox\Notification\Listeners;

use Illuminate\Support\Facades\Notification;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Notifications\ApproveNotification;

class ModelApprovedListener
{
    /**
     * handle.
     *
     * @param  mixed $model
     * @return void
     */
    public function handle($model): void
    {
        if (!$model instanceof Content) {
            return;
        }

        $context = user();

        if ($context->entityId() == $model->userId()) {
            return;
        }

        if (!method_exists($model, 'toApprovedNotification')) {
            return;
        }

        [$users, $module] = $model->toApprovedNotification();
        if ($module instanceof ApproveNotification) {
            $module->setContext($context);
            $response = [$users, $module];

            Notification::send(...$response);
        }
    }
}
