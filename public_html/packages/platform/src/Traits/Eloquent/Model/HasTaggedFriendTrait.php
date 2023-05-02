<?php

namespace MetaFox\Platform\Traits\Eloquent\Model;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\User\Models\UserEntity;

/**
 * @mixin HasTaggedFriend
 * @mixin Content
 */
trait HasTaggedFriendTrait
{
    protected int $isReview;

    public function toMail(MailMessage $service, ?UserEntity $user, ?UserEntity $owner): MailMessage
    {
        $friendName = $owner instanceof UserEntity ? $owner->name : null;
        $yourName   = $user instanceof UserEntity ? $user->name : null;

        $emailTitle = __p('core::phrase.username_tagged_you_in_a_post_subject', [
            'username' => $yourName,
            'item'     => 'post',
        ]);

        $emailLine = __p('core::phrase.hi_friend_username_tagged_you_in_a_post', [
            'friend'   => $friendName,
            'username' => $yourName,
            'item'     => 'post',
        ]);

        $url = $this->toUrl();

        if ($this->activity_feed != null) {
            $url = $this->activity_feed->toUrl();
        }

        return $service
            ->subject($emailTitle)
            ->line($emailLine)
            ->action(__p('core::phrase.review_now'), $url ?? '');
    }

    /**
     * @param  UserEntity $user
     * @param  UserEntity $owner
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toCallbackMessage(UserEntity $user, UserEntity $owner): string
    {
        $yourName = $user->name;
        $owner    = $owner->detail;

        if ($owner instanceof HasPrivacyMember) {
            return __p('core::phrase.username_tagged_entity_type_title_in_a_post_review_now', [
                'username'     => $yourName,
                'entity_type'  => $owner->entityType(),
                'entity_title' => $owner->toTitle(),
                'is_review'    => $this->isReview(),
            ]);
        }

        return __p('core::phrase.username_tagged_you_in_a_post_review_now', [
            'username'  => $yourName,
            'is_review' => $this->isReview(),
        ]);
    }

    /**
     * @return string|null
     */
    public function toTagFriendUrl(): ?string
    {
        if ($this->activity_feed instanceof Content) {
            return $this->activity_feed->toUrl();
        }

        return $this->toUrl();
    }

    /**
     * @return string|null
     */
    public function toTagFriendLink(): ?string
    {
        if ($this->activity_feed instanceof Content) {
            return $this->activity_feed->toLink();
        }

        return $this->toLink();
    }

    /**
     * @return string|null
     */
    public function toTagFriendRouter(): ?string
    {
        if ($this->activity_feed instanceof Content) {
            return $this->activity_feed->toRouter();
        }

        return $this->toRouter();
    }

    public function hasTagStream(): bool
    {
        return true;
    }

    public function setIsReview(int $isReview)
    {
        return $this->isReview = $isReview;
    }

    public function isReview(): int
    {
        return $this->isReview;
    }
}
