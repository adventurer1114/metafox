<?php

namespace MetaFox\Forum\Repositories\Eloquent;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use MetaFox\Forum\Jobs\DeleteForum;
use MetaFox\Forum\Models\Forum;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Forum\Policies\ForumPolicy;
use MetaFox\Forum\Repositories\ForumPostRepositoryInterface;
use MetaFox\Forum\Repositories\ForumRepositoryInterface;
use MetaFox\Forum\Repositories\ForumThreadRepositoryInterface;
use MetaFox\Forum\Support\Facades\Forum as ForumFacade;
use MetaFox\Forum\Support\ForumSupport;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFox;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class GroupRepository.
 * @method Forum getModel()
 * @method Forum find($id, $columns = ['*'])()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @inore
 */
class ForumRepository extends AbstractRepository implements ForumRepositoryInterface
{
    public function model()
    {
        return Forum::class;
    }

    /**
     * @throws AuthorizationException
     */
    public function viewForums(User $context): array
    {
        policy_authorize(ForumPolicy::class, 'viewAny', $context);

        $items = $this->getForumsForView($context);

        return $items;
    }

    public function viewForumsInAdminCP(User $context, array $attributes = []): Collection
    {
        policy_authorize(ForumPolicy::class, 'viewAdminCP', $context);

        $parentId = Arr::get($attributes, 'parent_id', 0);

        return Forum::query()
            ->with(['parentForums'])
            ->where([
                'parent_id' => $parentId,
            ])
            ->orderBy('parent_id')
            ->orderBy('ordering')
            ->get();
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function getForumsForNavigation(User $context, array $attributes): ?Collection
    {
        policy_authorize(ForumPolicy::class, 'viewAny', $context);

        return localCacheStore()->rememberForever(ForumFacade::getNavigationCacheId(), function () use ($attributes) {
            return ForumFacade::buildForumsForNavigation(0, $attributes);
        });
    }

    public function viewForum(User $context, int $id): Forum
    {
        // TODO: Implement viewForum() method.
    }

    public function createForum(User $context, array $attributes): Forum
    {
        policy_authorize(ForumPolicy::class, 'create', $context);

        $parentId = (int) Arr::get($attributes, 'parent_id', 0);

        $ordering = $this->getNextOrdering($parentId);

        $level = $this->getNextLevel($parentId);

        $attributes = array_merge($attributes, [
            'parent_id' => $parentId,
            'ordering'  => $ordering,
            'level'     => $level,
        ]);

        $forum = $this->getModel()->newModelInstance($attributes);

        $forum->save();

        $forum->refresh();

        if ($parentId) {
            $this->increaseTotal($parentId, 'total_sub');
        }

        $this->clearCaches();

        return $forum;
    }

    protected function getNextOrdering(int $parentId): int
    {
        $last = Forum::query()
            ->where('parent_id', '=', $parentId)
            ->orderByDesc('ordering')
            ->first();

        if (null === $last) {
            return 1;
        }

        return $last->ordering + 1;
    }

    protected function getNextLevel(int $parentId): int
    {
        if (0 === $parentId) {
            return 1;
        }

        $parent = Forum::query()
            ->where('id', '=', $parentId)
            ->first();

        if (null === $parent) {
            return 1;
        }

        return $parent->level + 1;
    }

    public function updateForum(User $context, int $id, array $attributes): Forum
    {
        $forum = $this->find($id);

        policy_authorize(ForumPolicy::class, 'update', $context, $forum);

        $oldParentId = $forum->parent_id;

        /*
         * In case
         */
        if (Arr::get($attributes, 'parent_id') != $forum->parent_id
            && $forum->subForums()->exists()
            && !$forum->parentForums()->exists()) {
            unset($attributes['parent_id']);
        }

        $newParentId = Arr::get($attributes, 'parent_id');

        $forum->fill($attributes);

        $forum->save();

        $forum->refresh();

        if ($oldParentId != $newParentId) {
            $subs = $forum->total_sub + 1;
            $this->increaseTotal($newParentId, 'total_sub', $subs);
            $this->increaseTotal($newParentId, 'total_comment', $forum->total_comment);
            $this->increaseTotal($newParentId, 'total_thread', $forum->total_thread);
            $this->decreaseTotal($oldParentId, 'total_sub', $subs);
            $this->decreaseTotal($oldParentId, 'total_comment', $forum->total_comment);
            $this->decreaseTotal($oldParentId, 'total_thread', $forum->total_thread);
        }

        $this->clearCaches();

        return $forum;
    }

    public function deleteForum(User $context, int $id, string $deleteOption, ?int $alternativeId = null): bool
    {
        $forum = $this->find($id);

        policy_authorize(ForumPolicy::class, 'delete', $context, $forum);

        $forum->delete();

        $this->clearCaches();

        DeleteForum::dispatch($id, $deleteOption, $alternativeId);

        return true;
    }

    /**
     * @throws AuthorizationException
     */
    public function getForumsForView(User $context): array
    {
        if (!policy_check(ForumPolicy::class, 'viewAny', $context)) {
            return [];
        }

        if (MetaFox::isMobile()) {
            return localCacheStore()->rememberForever(ForumFacade::getViewMobileCacheId(), function () {
                return ForumFacade::buildForumsForViewMobile();
            });
        }

        return localCacheStore()->rememberForever(ForumFacade::getViewCacheId(), function () {
            return ForumFacade::buildForumsForView();
        });
    }

    public function getForumsForDeleteOption(Forum $forum): array
    {
        return $this->getModel()->newQuery()
            ->select('id as value', 'title as label', 'parent_id', 'is_closed', 'level', 'ordering')
            ->orderBy('ordering')
            ->where('level', '<=', $forum->level)
            ->where('id', '<>', $forum->entityId())
            ->get()
            ->map(function ($forum) {
                $forum->is_active = !$forum->is_closed;

                return $forum;
            })
            ->toArray();
    }

    /**
     * @throws AuthorizationException
     */
    public function getForumsForForm(User $context, ?Forum $forum = null, bool $filterClosed = true): array
    {
        $isEdit = $forum instanceof Forum;

        if ($isEdit && $forum->subForums()->exists() && !$forum->parentForums()->exists()) {
            return [];
        }

        $query = $this->getModel()->newQuery()
            ->select('id as value', 'title as label', 'parent_id', 'is_closed', 'level', 'ordering')
            ->orderBy('ordering');

        if ($filterClosed) {
            $query->where('is_closed', MetaFoxConstant::IS_INACTIVE);
        }

        switch ($isEdit) {
            case true:
                $forums = $query->where('level', '<=', $forum->level)
                    ->where('id', '<>', $forum->entityId())
                    ->get()
                    ->toArray();
                break;
            default:
                $forums = localCacheStore()->rememberForever(ForumFacade::getFormCacheId(), function () use ($query) {
                    return $query
                        ->get()
                        ->toArray();
                });
        }

        if (!count($forums)) {
            return [];
        }

        return array_map(function ($forum) {
            return array_merge($forum, [
                'is_active' => !$forum['is_closed'],
            ]);
        }, $forums);
    }

    /**
     * @throws AuthorizationException
     */
    public function getSubForums(User $context, int $parentId, int $limit = 4): ?Paginator
    {
        $parent = $this->find($parentId);

        policy_authorize(ForumPolicy::class, 'view', $context, $parent);

        $query = $this->getModel()->newQuery();

        $items = $query
            ->where([
                'parent_id' => $parentId,
            ])
            ->orderBy('ordering');

        return $items->simplePaginate($limit);
    }

    protected function addMoreAttributes(array $attributes): array
    {
        if (!Arr::has($attributes, 'thread_id')) {
            Arr::set($attributes, 'thread_id', 0);
        }
        if (!Arr::has($attributes, 'post_id')) {
            Arr::set($attributes, 'post_id', 0);
        }

        return $attributes;
    }

    public function getSearchItems(User $context, array $attributes): Paginator
    {
        $owner = $context;

        switch ($attributes['item_type']) {
            case ForumSupport::SEARCH_BY_POST:
                $repository = resolve(ForumPostRepositoryInterface::class);
                $attributes = $this->addMoreAttributes($attributes);
                $items      = $repository->viewPosts($context, $owner, $attributes);
                break;
            default:
                $repository = resolve(ForumThreadRepositoryInterface::class);
                $items      = $repository->viewThreads($context, $owner, $attributes);
                break;
        }

        return $items;
    }

    protected function clearCaches(): void
    {
        localCacheStore()->deleteMultiple([
            ForumFacade::getNavigationCacheId(),
            ForumFacade::getViewCacheId(),
            ForumFacade::getFormCacheId(),
            ForumFacade::getViewMobileCacheId(),
        ]);
    }

    public function order(User $context, array $orderIds): bool
    {
        policy_authorize(ForumPolicy::class, 'update', $context);

        $ordering = 1;

        foreach ($orderIds as $id) {
            Forum::query()
                ->where('id', '=', $id)
                ->update(['ordering' => $ordering++]);
        }

        $this->clearCaches();

        return true;
    }

    public function close(User $context, int $id, bool $closed): ?Forum
    {
        $forum = $this->find($id);

        policy_authorize(ForumPolicy::class, 'update', $context, $forum);

        $forum->fill(['is_closed' => $closed]);

        $forum->save();

        $this->clearCaches();

        return $forum;
    }

    public function getAscendantIds(int $forumId, bool $includeSelf = true): array
    {
        $forum = Forum::query()
            ->withTrashed()
            ->where('id', '=', $forumId)
            ->first();

        if (null === $forum) {
            return [];
        }

        $results = [];

        if ($includeSelf) {
            $results[] = $forumId;
        }

        if (0 == $forum->parent_id) {
            return $results;
        }

        $forums = Forum::query()
            ->withTrashed()
            ->get()
            ->pluck('parent_id', 'id')
            ->toArray();

        $parentId = $forum->parent_id;

        do {
            $results[] = $parentId;
            $parentId  = Arr::get($forums, $parentId);
        } while ($parentId);

        return $results;
    }

    public function getDescendantIds(int $forumId): array
    {
        return array_unique(ForumFacade::buildForumIdsForSearch($forumId));
    }

    public function getBreadcrumbs(int $forumId): array
    {
        $ids = $this->getAscendantIds($forumId, false);

        if (!count($ids)) {
            return [];
        }

        $ids = array_reverse($ids);

        $forums = $this->getModel()->newQuery()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        if (!$forums->count()) {
            return [];
        }

        $breadcrumbs = [];

        foreach ($ids as $id) {
            $forum = $forums->get($id);

            if (!$forum instanceof Forum) {
                continue;
            }

            $breadcrumbs[] = [
                'label' => $forum->toTitle(),
                'to'    => $forum->toLink(),
            ];
        }

        return $breadcrumbs;
    }

    public function increaseTotal(int $forumId, string $column, int $total = 1): void
    {
        $forumIds = $this->getAscendantIds($forumId);

        if (!count($forumIds)) {
            return;
        }

        Forum::query()
            ->whereIn('id', $forumIds)
            ->get()
            ->each(function ($forum) use ($column, $total) {
                $forum->incrementAmount($column, $total);
            });

        $this->clearCaches();
    }

    public function decreaseTotal(int $forumId, string $column, int $total = 1): void
    {
        $forumIds = $this->getAscendantIds($forumId);

        if (!count($forumIds)) {
            return;
        }

        Forum::query()
            ->whereIn('id', $forumIds)
            ->get()
            ->each(function ($forum) use ($column, $total) {
                $forum->decrementAmount($column, $total);
            });

        $this->clearCaches();
    }

    public function migrateStatistics(int $level): void
    {
        $forums = Forum::query()
            ->with(['subForums'])
            ->withTrashed()
            ->where([
                'level' => $level,
            ])
            ->get();

        if (!$forums->count()) {
            return;
        }

        foreach ($forums as $forum) {
            $threads = ForumThread::query()
                ->withCount([
                    'posts' => function ($builder) {
                        $builder->where('is_approved', '=', 1);
                    },
                ])
                ->where('forum_id', '=', $forum->entityId())
                ->where('is_approved', '=', 1)
                ->get();

            $totalThreads = $totalComments = 0;

            $totalSubs = $forum->subForums->count();

            if ($threads->count()) {
                $totalThreads = $threads->count();

                foreach ($threads as $thread) {
                    $totalComments += $thread->posts_count;
                }
            }

            if ($totalSubs) {
                foreach ($forum->subForums as $subForum) {
                    $totalThreads += $subForum->total_thread;
                    $totalComments += $subForum->total_comment;
                    $totalSubs += $subForum->total_sub;
                }
            }

            $forum->update([
                'total_thread'  => $totalThreads,
                'total_comment' => $totalComments,
                'total_sub'     => $totalSubs,
            ]);
        }

        $this->migrateStatistics($level - 1);
    }

    public function migrateForumLevel(int $level = 1): void
    {
        try {
            $condition = [
                'level' => $level,
            ];
            if ($level === 1) {
                $condition['parent_id'] = 0;
            }
            $forums = Forum::query()->with(['subForums'])
                ->withTrashed()
                ->where($condition)
                ->cursor();
            $batch = [];
            if ($forums->isEmpty() || $level == 999) {
                return;
            }
            foreach ($forums as $forum) {
                foreach ($forum->subForums as $subForum) {
                    $batch[] = [
                        'id'    => $subForum->id,
                        'level' => $level + 1,
                        'title' => $subForum->title,
                    ];
                }
            }
            Forum::query()->upsert($batch, ['id']);

            $this->migrateForumLevel($level + 1);
        } catch (Exception $e) {
            return;
        }
    }

    public function paginateForums(array $attributes = []): Paginator
    {
        $limit    = Arr::get($attributes, 'limit', 3);
        $parentId = Arr::get($attributes, 'parent_id', 0);

        return $this->getModel()->newQuery()
            ->where([
                'parent_id' => $parentId,
            ])
            ->orderBy('ordering')
            ->paginate($limit, ['forums.*']);
    }

    public function countActiveForumByLevel(int $level): int
    {
        return $this->getModel()
            ->newQuery()
            ->where([
                'level'     => $level,
                'is_closed' => 0,
            ])->count();
    }
}
