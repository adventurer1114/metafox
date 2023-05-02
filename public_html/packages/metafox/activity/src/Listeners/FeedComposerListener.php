<?php

namespace MetaFox\Activity\Listeners;

use MetaFox\Activity\Models\Post;
use MetaFox\Activity\Repositories\FeedRepositoryInterface;
use MetaFox\Activity\Repositories\PostRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;

class FeedComposerListener
{
    /**
     * @param  User             $user
     * @param  User             $owner
     * @param  string           $postType
     * @param  array            $params
     * @return array|int[]|null
     */
    public function handle(User $user, User $owner, string $postType, array $params): ?array
    {
        if ($postType != Post::FEED_POST_TYPE) {
            return null;
        }

        $post = resolve(PostRepositoryInterface::class)->createPost($user, $owner, $params);

        $post->load('activity_feed');

        return [
            'id' => $post->activity_feed ? $post->activity_feed?->entityId() : 0,
        ];
    }
}
