<?php

namespace MetaFox\Forum\Listeners;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use MetaFox\Forum\Models\Forum;
use MetaFox\Forum\Models\ForumPost;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Forum\Repositories\ForumPostRepositoryInterface;
use MetaFox\Forum\Repositories\ForumRepositoryInterface;
use MetaFox\Forum\Repositories\ForumThreadRepositoryInterface;
use MetaFox\Forum\Support\Facades\Forum as ForumFacade;

class ModelApprovedListener
{
    public function handle(Model $model): void
    {
        if ($model->entityType() == ForumPost::ENTITY_TYPE) {
            $this->handleForumPost($model);

            return;
        }

        if ($model->entityType() == ForumThread::ENTITY_TYPE) {
            $this->handleForumThread($model);
        }
    }

    protected function handleForumThread(ForumThread $thread): void
    {
        $forum = $thread->forum;

        if (!$forum instanceof Forum) {
            return;
        }

        resolve(ForumRepositoryInterface::class)->increaseTotal($forum->entityId(), 'total_thread');

        ForumFacade::clearCaches($forum->entityId());
    }

    protected function handleForumPost(ForumPost $model): void
    {
        $thread = $model->thread;

        if (null !== $thread) {
            if ($model->is_approved) {
                $thread->incrementAmount('total_comment');

                if (null !== $thread->forum) {
                    resolve(ForumRepositoryInterface::class)->increaseTotal($thread->forum->entityId(), 'total_comment');
                }

                resolve(ForumThreadRepositoryInterface::class)->updatePostId($thread);
            }

            resolve(ForumPostRepositoryInterface::class)->sendNotificationForThreadSubscription($thread->entityId(), $model->entityId());
        }
    }
}
