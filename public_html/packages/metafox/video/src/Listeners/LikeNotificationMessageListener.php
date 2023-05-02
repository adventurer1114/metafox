<?php

namespace MetaFox\Video\Listeners;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Models\UserEntity;
use MetaFox\Video\Models\Video;

/**
 * Class LikeNotificationToCallbackMessageListener.
 * @ignore
 */
class LikeNotificationMessageListener
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

        if (!$content instanceof Video) {
            return null;
        }

        $friendName = $user->name;
        $title      = $content->toTitle();
        $locale     = $context->preferredLocale();

        /**
         * @var string|null $name
         */
        $name = $content->owner->hasNamedNotification();

        if ($name) {
            return __p('like::notification.user_reacted_to_your_item_type_in_name', [
                'user'       => $friendName,
                'owner_name' => $content->ownerEntity->name,
                'content'    => $title,
                'item_type'  => $content->entityType(),
            ], $locale);
        }

        // Default message in case no event data is returned
        return __p('like::notification.user_reacted_to_your_item_type', [
            'user'      => $friendName,
            'title'     => $title,
            'item_type' => $content->entityType(),
        ], $locale);
    }
}
