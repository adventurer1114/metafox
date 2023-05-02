<?php

namespace MetaFox\Friend\Notifications;

use MetaFox\Friend\Models\FriendRequest;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;
use MetaFox\User\Models\UserEntity;

/**
 * Class FriendAccepted.
 *
 * @property FriendRequest $model
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 */
class FriendAccepted extends Notification
{
    protected string $type = 'friend_accepted';

    public function callbackMessage(): ?string
    {
        $userEntity = $this->model->userEntity;

        $friendFullName = $userEntity instanceof UserEntity ? $userEntity->name : null;

        return $this->localize('friend::phrase.username_accepted_your_friend_request', ['username' => $friendFullName]);
    }

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

    public function toUrl(): ?string
    {
        $user = $this->model->user;

        return $user->toUrl();
    }

    public function toLink(): ?string
    {
        $user = $this->model->user;

        return $user->toLink();
    }
}
