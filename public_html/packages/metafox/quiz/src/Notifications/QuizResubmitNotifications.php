<?php

namespace MetaFox\Quiz\Notifications;

use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;
use MetaFox\Quiz\Models\Quiz;

/**
 * Class QuizResubmitNotifications.
 *
 * @property Quiz $model
 */
class QuizResubmitNotifications extends Notification
{
    protected string $type = 'quiz_resubmit_notification';

    public function toArray(IsNotifiable $notifiable): array
    {
        return [
            'data'      => $this->model->toArray(),
            'item_id'   => $this->model->entityId(),
            'item_type' => $this->model->entityType(),
            'user_id'   => $this->model->userId(),
            'user_type' => $this->model->userType(),
        ];
    }

    public function callbackMessage(): ?string
    {
        return $this->localize(
            'quiz::notification.quiz_title_has_changed_please_resubmit_your_answers',
            ['title' => $this->model->title]
        );
    }
}
