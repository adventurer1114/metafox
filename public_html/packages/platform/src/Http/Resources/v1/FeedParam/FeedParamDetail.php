<?php

namespace MetaFox\Platform\Http\Resources\v1\FeedParam;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Activity\Traits\RelatedCommentsTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasUrl;
use MetaFox\Platform\Traits\Helpers\IsFriendTrait;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Helpers\UserReactedTrait;

/**
 * @property Content $resource
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FeedParamDetail extends JsonResource
{
    use IsLikedTrait;
    use IsFriendTrait;
    use UserReactedTrait;
    use RelatedCommentsTrait;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string,           mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $context = user();

        $resource = $this->resource;

        $reactItem = $resource->reactItem();

        $link = url_utility()->makeApiResourceUrl($resource->entityType(), $resource->entityId());

        $url = url_utility()->makeApiResourceFullUrl($resource->entityType(), $resource->entityId());

        if ($resource instanceof HasUrl) {
            $link = $resource->toLink();
            $url  = $resource->toUrl();
        }

        $privacyDetail = null;

        if ($resource instanceof Content) {
            $privacyDetail = app('events')->dispatch('activity.get_privacy_detail', [
                $context,
                $resource,
                $resource->owner?->getRepresentativePrivacy(),
                true,
            ], true);
        }

        return [
            'item_id'                    => $this->resource->entityId(),
            'comment_type_id'            => $reactItem->entityType(),
            'comment_item_id'            => $reactItem->entityId(),
            'total_comment'              => $reactItem instanceof HasTotalComment ? $reactItem->total_comment : 0,
            'total_like'                 => $reactItem instanceof HasTotalLike ? $reactItem->total_like : 0,
            'total_reply'                => $reactItem instanceof HasTotalCommentWithReply ? $reactItem->total_reply : 0,
            'like_type_id'               => $reactItem->entityType(),
            'like_item_id'               => $reactItem->entityId(),
            'feed_title'                 => '',
            'feed_link'                  => $link,
            'feed_url'                   => $url,
            'feed_is_liked'              => $this->isLike($context, $reactItem),
            'feed_is_friend'             => $this->isFriend($context, $this->resource->user),
            'report_module'              => $this->resource->entityType(),
            'like_phrase'                => '',
            'related_comments'           => $this->relatedCommentsItemDetail($context, $reactItem),
            'related_comments_statistic' => $this->relatedCommentsHiddenStatistics($context, $reactItem),
            'user_reacted'               => $this->userReacted($context, $reactItem),
            'most_reactions'             => $this->userMostReactions($context, $reactItem),
            'privacy_detail'             => $privacyDetail,
        ];
    }
}
