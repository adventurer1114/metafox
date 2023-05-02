<?php

namespace MetaFox\Activity\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;

trait RelatedCommentsTrait
{
    public function relatedComments(User $context, ?Entity $content = null, array $extra = []): JsonResource
    {
        if (!$content instanceof HasTotalComment) {
            return new JsonResource([]);
        }

        if (Settings::get('comment.prefetch_comments_on_feed') <= 0) {
            return new JsonResource([]);
        }

        /** @var JsonResource|mixed $response */
        $response = app('events')->dispatch('comment.related_comments', [$context, $content, $extra], true);

        if (!$response instanceof JsonResource) {
            return new JsonResource([]);
        }

        return $response;
    }

    public function relatedCommentsItemDetail(User $context, ?Entity $content = null, int $limit = 6): JsonResource
    {
        if (!$content instanceof HasTotalComment) {
            return new JsonResource([]);
        }
        /** @var JsonResource|mixed $response */
        $response = app('events')->dispatch(
            'comment.related_comments.item_detail',
            [$context, $content, $limit],
            true
        );

        if (!$response instanceof JsonResource) {
            return new JsonResource([]);
        }

        return $response;
    }

    public function relatedCommentsHiddenStatistics(
        User $context,
        ?Entity $content = null,
        int $commentParentId = 0
    ): array {
        if (!$content instanceof HasTotalComment) {
            return [];
        }

        $statistics = [
            'total_hidden' => 0,
        ];

        /** @var JsonResource|mixed $response */
        $totalHidden = app('events')->dispatch(
            'comment.related_comments.total_hidden',
            [$context, $content, $commentParentId],
            true
        );

        if (is_numeric($totalHidden)) {
            Arr::set($statistics, 'total_hidden', $totalHidden);
        }

        return $statistics;
    }
}
