<?php

namespace MetaFox\Forum\Observers;

use MetaFox\Forum\Models\ForumPost;
use MetaFox\Forum\Models\ForumPostText;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Forum\Repositories\ForumRepositoryInterface;

/**
 * Class ForumPostObserver.
 */
class ForumPostObserver
{
    public function deleted(ForumPost $post)
    {
        $postText = $post->postText;

        if ($postText instanceof ForumPostText) {
            $postText->delete();
        }

        $thread = $post->thread;

        if ($thread instanceof ForumThread) {
            $thread->decrementAmount('total_comment');

            if (null !== $thread->forum) {
                resolve(ForumRepositoryInterface::class)->decreaseTotal($thread->forum->entityId(), 'total_comment');
            }
        }

        app('events')->dispatch('notification.delete_mass_notification_by_item', [$post], true);

        $attachments = $post->attachments;
        if (null !== $attachments) {
            foreach ($attachments as $attachment) {
                $attachment->delete();
            }
        }

        $post->quoteData()->delete();
    }

    public function created(ForumPost $post)
    {
        $thread = $post->thread;

        if ($thread instanceof ForumThread && $post->is_approved) {
            $thread->incrementAmount('total_comment');

            if (null !== $thread->forum) {
                resolve(ForumRepositoryInterface::class)->increaseTotal($thread->forum->entityId(), 'total_comment');
            }
        }
    }
}
