<?php

namespace MetaFox\Forum\Support;

use MetaFox\Forum\Contracts\ForumPostSupportContract;
use MetaFox\Forum\Models\ForumPost;
use MetaFox\Forum\Repositories\ForumPostRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\ResourcePermission;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;

class ForumPostSupport implements ForumPostSupportContract
{
    public const CAN_QUOTE = 'can_quote';

    protected ForumPostRepositoryInterface $repository;

    public function __construct(ForumPostRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getCustomExtra(User $user, ForumPost $model): array
    {
        $thread = $model->thread;

        if (null === $thread) {
            return [];
        }

        $models = [$model, $model];

        return [
            ResourcePermission::CAN_EDIT            => $user->can('update', $models),
            ResourcePermission::CAN_LIKE            => $user->can('like', $models),
            ResourcePermission::CAN_SHARE           => $this->canShare($user, $model),
            ResourcePermission::CAN_DELETE          => $user->can('delete', $models),
            ResourcePermission::CAN_REPORT          => $user->can('reportItem', $models),
            ResourcePermission::CAN_REPORT_TO_OWNER => $user->can('reportToOwner', $models),
            ResourcePermission::CAN_APPROVE         => $user->can('approve', $models),
            self::CAN_QUOTE                         => $user->can('quote', $models),
            ResourcePermission::CAN_SAVE_ITEM       => $user->can('saveItem', $models),
        ];
    }

    public function deletePost(User $context, int $id): bool
    {
        return $this->repository->deletePost($context, $id);
    }

    public function getRelations(): array
    {
        return ['postText', 'thread', 'quotePost', 'quoteData'];
    }

    protected function canShare(User $user, ForumPost $model): bool
    {
        //Checking sharing permission for all before checking sharing permission each item type
        if (!$user->hasPermissionTo('share.create')) {
            return false;
        }

        if (!$user->hasPermissionTo('forum_post.share')) {
            return false;
        }

        $owner = $model->owner;

        if (!$owner instanceof User) {
            return true;
        }

        if ($owner->entityId() == $user->entityId()) {
            return true;
        }

        return PrivacyPolicy::checkCreateOnOwner($user, $owner);
    }
}
