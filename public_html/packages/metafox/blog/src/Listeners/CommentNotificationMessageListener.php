<?php

namespace MetaFox\Blog\Listeners;

use MetaFox\Blog\Models\Blog;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Models\UserEntity;

/**
 * Class LikeNotificationToCallbackMessageListener.
 * @ignore
 */
class CommentNotificationMessageListener
{
    /**
     * @param User|null       $context
     * @param UserEntity|null $user
     * @param Content|null    $content
     *
     * @return string|null
     */
    public function handle(?User $context, ?UserEntity $user = null, ?Content $content = null): ?string
    {
        if (!$context) {
            return null;
        }

        if (!$user instanceof UserEntity) {
            return null;
        }

        $locale     = $context->preferredLocale();
        $friendName = $user->name;

        if (!$content instanceof Blog) {
            return null;
        }
        $title        = $content->toTitle();
        $owner        = $content->owner;
        $userName     = __p('comment::phrase.your', [], $locale);
        $isThemselves = 1;

        /* @var string|null $ownerType */
        $ownerType = $owner->hasNamedNotification();

        if ($content->userId() != $context->entityId()) {
            $userName     = $content->userEntity->name;
            $isThemselves = 0;
        }

        if ($ownerType) {
            return __p('comment::notification.user_commented_on_your_item_type_in_owner_type', [
                'user'          => $friendName,
                'owner_type'    => $ownerType,
                'owner_name'    => $content->ownerEntity->name,
                'user_name'     => $userName,
                'item_type'     => $content->entityType(),
                'is_themselves' => $isThemselves,
            ], $locale);
        }

        // Default message in case no event data is returned
        return __p('comment::notification.user_commented_on_your_item_type_title', [
            'user'          => $friendName,
            'title'         => $title,
            'user_name'     => $userName,
            'item_type'     => $content->entityType(),
            'is_themselves' => $isThemselves,
        ], $locale);
    }
}
