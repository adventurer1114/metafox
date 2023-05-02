<?php

namespace MetaFox\Like\Repositories\Eloquent;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MetaFox\Like\Http\Resources\v1\Reaction\ReactionDetail;
use MetaFox\Like\Http\Resources\v1\Reaction\ReactionItemCollection;
use MetaFox\Like\Models\Like;
use MetaFox\Like\Models\LikeAgg;
use MetaFox\Like\Policies\LikePolicy;
use MetaFox\Like\Policies\ReactionPolicy;
use MetaFox\Like\Repositories\LikeRepositoryInterface;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use stdClass;

/**
 * Class LikeRepository.
 * @method Like getModel()
 * @method Like find($id, $columns = ['*'])
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 */
class LikeRepository extends AbstractRepository implements LikeRepositoryInterface
{
    public function model(): string
    {
        return Like::class;
    }

    public function viewLikes(User $context, array $attributes): Paginator
    {
        policy_authorize(LikePolicy::class, 'viewAny', $context);

        $limit = $attributes['limit'];
        $itemId = $attributes['item_id'];
        $itemType = $attributes['item_type'];
        $reactionId = $attributes['react_id'];

        $like = new Like([
            'item_id'   => $itemId,
            'item_type' => $itemType,
        ]);

        $item = $like->item;

        if (null == $item) {
            throw (new ModelNotFoundException())->setModel($itemType);
        }

        $query = $this->getModel()->newQuery()
            ->where('item_id', $item->entityId())
            ->where('item_type', $item->entityType());

        if ($reactionId > 0) {
            $query->where('likes.reaction_id', $reactionId);
        }

        return $query->with(['user', 'reaction'])->simplePaginate($limit);
    }

    public function viewLikeTabs(User $context, int $itemId, string $itemType): array
    {
        policy_authorize(LikePolicy::class, 'viewAny', $context);

        $likeAgg = new LikeAgg([
            'item_id'   => $itemId,
            'item_type' => $itemType,
        ]);

        $item = $likeAgg->item;

        if (null == $item) {
            throw (new ModelNotFoundException())->setModel($itemType);
        }

        $likeTabs = LikeAgg::query()->with('reaction')
            ->where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->where('total_reaction', '>', 0)
            ->get();

        $totalReaction = 0;

        $likeTabs = $likeTabs->map(function (LikeAgg $item) use (&$totalReaction) {
            $reaction = $item->reaction;

            $totalReaction += $item->total_reaction;

            return [
                'id'            => $reaction->entityId(),
                'title'         => __p($reaction->title),
                'total_reacted' => $item->total_reaction,
                'icon'          => $reaction->icon,
                'color'         => "#$reaction->color",
            ];
        })->toArray();

        if (count($likeTabs) > 1) {
            $tabAll = [
                [
                    'id'            => 0,
                    'title'         => __p('core::phrase.all'),
                    'total_reacted' => $totalReaction,
                    'icon'          => null,
                    'color'         => null,
                ],
            ];

            $likeTabs = array_merge($tabAll, $likeTabs);
        }

        return $likeTabs;
    }

    public function isLiked(User $context, HasTotalLike $content): bool
    {
        return $this->getModel()->newQuery()->where([
            'item_id'   => $content->entityId(),
            'item_type' => $content->entityType(),
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
        ])->exists();
    }

    public function getLike(User $context, HasTotalLike $content): ?Like
    {
        $like = $this->getModel()->newQuery()->with('reaction')
            ->where([
                'item_id'   => $content->entityId(),
                'item_type' => $content->entityType(),
                'user_id'   => $context->entityId(),
                //'user_type' => $context->entityType(),
            ])->first();

        if (!$like instanceof Like) {
            return null;
        }

        return $like;
    }

    public function createLike(User $context, int $itemId, string $itemType, int $reactionId): array
    {
        $checkItem = new Like([
            'item_id'   => $itemId,
            'item_type' => $itemType,
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
        ]);

        $item = $checkItem->item;

        if (null == $item) {
            abort(404, __p('core::phrase.this_post_is_no_longer_available'));
        }

        policy_authorize(LikePolicy::class, 'create', $context, $item);

        $like = $this->processLike($context, $item, $reactionId);

        $item->refresh();

        $totalLike = 0;
        $reactions = null;
        if ($item instanceof HasTotalLike) {
            $totalLike = $item->total_like;
            $reactions = $this->getMostReactions($context, $item);
        }

        $feedId = null;

        if ($item instanceof ActivityFeedSource) {
            try {
                /** @var Content $feed */
                $feed = app('events')->dispatch('activity.get_feed', [$context, $item], true);
                $feedId = $feed->entityId();
            } catch (Exception $e) {
                $feedId = null;
            }
        }

        return [
            'total_like'     => $totalLike,
            'like_phrase'    => '', //@todo: implement later if FE need it
            'is_liked'       => true,
            'feed_id'        => $feedId,
            'most_reactions' => $reactions !== null ? new ReactionItemCollection($reactions) : [],
            'user_reacted'   => $like != null ? (new ReactionDetail($like->reaction)) : (new stdClass()),
            'id'             => $like->entityId(),
            'item_id'        => $item->entityId(),
            'item_type'      => $item->entityType(),
        ];
    }

    private function processLike(User $context, Content $item, int $reactionId): Like
    {
        $params = [
            'item_id'   => $item->entityId(),
            'item_type' => $item->entityType(),
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
        ];

        $like = $this->getModel()->newQuery()->where($params)->first();

        if (!$like instanceof Like) {
            $like = new Like();
            $params = array_merge($params, [
                'owner_id'    => $item->userId(),
                'owner_type'  => $item->userType(),
                'reaction_id' => $reactionId,
            ]);
            $like->fill($params);
            $like->save();

            return $like;
        }

        if ($reactionId != $like->reaction_id) {
            $like->fill(['reaction_id' => $reactionId]);
            $like->save();
        }

        return $like;
    }

    public function deleteLikeById(User $context, int $id): bool
    {
        $like = $this->find($id);

        policy_authorize(LikePolicy::class, 'delete', $context, $like);

        return (bool) $like->delete();
    }

    public function deleteByUser(User $context): bool
    {
        return $this->getModel()
            ->where('user_id', $context->entityId())
            ->where('user_type', $context->entityType())
            ->each(function (Like $like) {
                $like->delete();
            });
    }

    /**
     * @param  User                          $context
     * @param  int                           $itemId
     * @param  string                        $itemType
     * @return array<string,          mixed>
     * @throws AuthorizationException
     */
    public function deleteByUserAndItem(User $context, int $itemId, string $itemType): array
    {
        /** @var Like $like */
        $like = $this->getModel()->newModelInstance()
            ->with(['item'])
            ->where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->where('user_id', $context->entityId())
            ->where('user_type', $context->entityType())
            ->firstOrFail();

        policy_authorize(LikePolicy::class, 'delete', $context, $like);

        $item = $like->item;
        $like->delete();
        $item->refresh();

        $totalLike = 0;
        $reactions = null;
        $feedId = null;

        if ($item instanceof HasTotalLike) {
            $totalLike = $item->total_like;
            $reactions = $this->getMostReactions($context, $item);
        }

        if ($item instanceof ActivityFeedSource) {
            try {
                /** @var Content $feed */
                $feed = app('events')->dispatch('activity.get_feed', [$context, $item], true);
                $feedId = $feed->entityId();
            } catch (Exception $e) {
                $feedId = null;
            }
        }

        return [
            'total_like'     => $totalLike,
            'like_phrase'    => '', //@todo: implement later if FE need it
            'is_liked'       => false,
            'feed_id'        => $feedId,
            'most_reactions' => $reactions !== null ? new ReactionItemCollection($reactions) : [],
        ];
    }

    public function getMostReactions(User $context, HasTotalLike $content, int $limit = 3): Collection
    {
        $results = new Collection();

        if (policy_check(ReactionPolicy::class, 'viewAny', $context) === false) {
            return $results;
        }

        /** @var LikeAgg[]|Collection $likeAggs */
        $likeAggs = LikeAgg::query()
            ->with(['reaction'])
            ->where([
                'item_id'   => $content->entityId(),
                'item_type' => $content->entityType(),
            ])
            ->where('total_reaction', '>', 0)
            ->orderBy('total_reaction', 'DESC')
            ->orderBy('reaction_id', 'ASC')
            ->limit($limit)
            ->get();

        if ($likeAggs->count() > 0) {
            foreach ($likeAggs as $likeAgg) {
                $results->add($likeAgg->reaction);
            }
        }

        return $results;
    }
}
