<?php

namespace MetaFox\Page\Notifications;

use MetaFox\Page\Models\Page as Model;
use MetaFox\Platform\Notifications\ApproveNotification;
use MetaFox\User\Support\Facades\UserEntity;

/**
 * Class AssignOwnerNotification.
 *
 * @property Model $model
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 */
class AssignOwnerNotification extends ApproveNotification
{
    protected string $type = 'assign_owner_page';

    public function callbackMessage(): ?string
    {
        $user = $owner = null;

        try {
            $owner = UserEntity::getById($this->model?->userId());
            $user  = $this->user ?? UserEntity::getById($this->userId);
        } catch (\Throwable $error) {
            logger()->error($error->getMessage());
        }

        if (!$owner) {
            return null;
        }

        if (!$user) {
            return null;
        }

        return $this->localize('page::notification.user_assign_owner_of_the_page_title_notification', [
            'user'  => $user->name,
            'owner' => $owner->name,
            'title' => $this->model->toTitle(),
        ]);
    }
}
