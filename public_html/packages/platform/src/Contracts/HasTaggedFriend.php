<?php

namespace MetaFox\Platform\Contracts;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\User\Models\UserEntity;

/**
 * Interface HasTaggedFriend.
 */
interface HasTaggedFriend extends Entity
{
    /**
     * @param  MailMessage     $service
     * @param  UserEntity|null $user
     * @param  UserEntity|null $owner
     * @return MailMessage
     */
    public function toMail(MailMessage $service, ?UserEntity $user, ?UserEntity $owner): MailMessage;

    /**
     * @param  UserEntity $user
     * @param  UserEntity $owner
     * @return string
     */
    public function toCallbackMessage(UserEntity $user, UserEntity $owner): string;

    /**
     * @return string|null
     */
    public function toTagFriendUrl(): ?string;

    /**
     * @return string|null
     */
    public function toTagFriendLink(): ?string;

    /**
     * @return string|null
     */
    public function toTagFriendRouter(): ?string;

    /**
     * @return bool
     */
    public function hasTagStream(): bool;

    /**
     * @param  int   $isReview
     * @return mixed
     */
    public function setIsReview(int $isReview);

    /**
     * @return int
     */
    public function isReview(): int;
}
