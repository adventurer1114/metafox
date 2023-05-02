<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Comment\Observers;

use MetaFox\Comment\Models\Comment;
use MetaFox\Comment\Repositories\CommentRepositoryInterface;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasAmounts;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;

class CommentObserver
{
    /**
     * @param Comment $model
     */
    public function created(Comment $model): void
    {
        $item = $model->item;

        if ($item instanceof HasTotalComment) {
            $item->incrementAmount('total_comment');
        }

        //update total_comment of parent.
        if ($model->parent_id > 0) {
            $parentComment = $model->parentComment;
            if ($parentComment instanceof HasAmounts) {
                $parentComment->incrementAmount('total_comment');
            }

            if ($item instanceof HasTotalCommentWithReply) {
                $item->incrementAmount('total_reply');
            }
        }

        $this->redundantFeed($item);
    }

    /**
     * @param Comment $model
     */
    public function deleted(Comment $model): void
    {
        $item = $model->item;

        $model->tagData()->sync([]);

        if ($item instanceof HasTotalComment) {
            $item->decrementAmount('total_comment');
        }

        //update total_comment of parent
        if ($model->parent_id > 0) {
            $parentComment = $model->parentComment;
            if ($parentComment instanceof HasAmounts) {
                $parentComment->decrementAmount('total_comment');
            }

            if ($item instanceof HasTotalCommentWithReply) {
                $item->decrementAmount('total_reply');
            }
        }

        //delete hide comment
        $model->commentHides()->delete();

        //delete comment attachment
        $commentAttachment = $model->commentAttachment;

        if (null != $commentAttachment) {
            // todo check to rollDown attachment
            $commentAttachment->delete();
        }

        //delete children
        $commentRepository = resolve(CommentRepositoryInterface::class);

        $commentRepository->deleteCommentByParentId($model->entityId());

        //delete history
        $model->commentHistory()->delete();
        $this->redundantFeed($item);
    }

    private function redundantFeed(?Entity $item): void
    {
        app('events')->dispatch('activity.redundant', [$item], true);
    }
}
