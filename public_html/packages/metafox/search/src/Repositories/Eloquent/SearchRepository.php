<?php

namespace MetaFox\Search\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MetaFox\Core\Repositories\Contracts\PrivacyMemberRepositoryInterface;
use MetaFox\Hashtag\Repositories\TagRepositoryInterface;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasHashTag;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\ResourcePostOnOwner;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFox;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\PrivacyScope;
use MetaFox\Platform\Support\Browse\Scopes\TagScope;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\Platform\UserRole;
use MetaFox\Search\Models\PrivacyStream;
use MetaFox\Search\Models\Search;
use MetaFox\Search\Repositories\SearchRepositoryInterface;
use MetaFox\Search\Support\StreamManager;
use MetaFox\Search\Support\Support;
use MetaFox\User\Models\UserEntity;
use MetaFox\User\Support\Facades\UserEntity as UserEntityFacade;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class SearchRepository.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 */
class SearchRepository extends AbstractRepository implements SearchRepositoryInterface
{
    public function model(): string
    {
        return Search::class;
    }

    private function getStreamManager(): StreamManager
    {
        return resolve(StreamManager::class);
    }

    /**
     * @param Content $item
     *
     * @return int[]|null
     */
    private function getPrivacyIds(Content $item): ?array
    {
        $itemId = $item->entityId();

        $itemType = $item->entityType();

        if ($itemType === 'feed') {
            $feedItem = $item->item;

            // In case item is not integrated to feed composer
            if (!$feedItem instanceof ResourcePostOnOwner) {
                return null;
            }

            $itemId = $feedItem->entityId();

            $itemType = $feedItem->entityType();
        }

        return app('events')->dispatch('core.get_privacy_id', [$itemId, $itemType], true);
    }

    /**
     * @throws ValidatorException
     */
    public function createdBy(HasGlobalSearch $item): void
    {
        // validate is item
        if (!$item instanceof Content) {
            return;
        }

        $data = $item->toSearchable();

        if (!$data) {
            return;
        }

        $coreData = [
            'item_id'    => $item->entityId(),
            'item_type'  => $item->entityType(),
            'user_id'    => $item->userId(),
            'user_type'  => $item->userType(),
            'owner_id'   => $item->ownerId(),
            'owner_type' => $item->ownerType(),
        ];

        $this->deleteWhere($coreData);

        $privacyIds = [MetaFoxPrivacy::NETWORK_PUBLIC_PRIVACY_ID];

        $data = array_merge($coreData, $data, [
            'privacy' => MetaFoxPrivacy::EVERYONE,
        ]);

        if ($item instanceof HasPrivacy) {
            Arr::set($data, 'privacy', $item->privacy);

            $privacyIds = $this->getPrivacyIds($item);
        }

        if (!is_array($privacyIds)) {
            return;
        }

        $model = $this->create($data);

        if (!$model instanceof Search) {
            return;
        }

        foreach ($privacyIds as $privacyId) {
            $this->createPrivacyStream($model->entityId(), $privacyId);
        }

        $this->handleTagData($model, $data);
    }

    protected function handleTagData(Search $search, array $attributes = []): void
    {
        $item = $search->item;

        if (!$item instanceof HasHashTag) {
            return;
        }

        $tagIds = app('events')->dispatch(
            'hashtag.create_hashtag',
            [$item->user, $item, Arr::get($attributes, 'text'), false, true],
            true
        );

        $search->tagData()->sync($tagIds);
    }

    /**
     * @throws ValidatorException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function updatedBy(HasGlobalSearch $item): void
    {
        // validate is item
        if (!$item instanceof Content) {
            return;
        }

        $data = $item->toSearchable();

        if (!$data) {
            return;
        }

        $coreData = [
            'item_id'    => $item->entityId(),
            'item_type'  => $item->entityType(),
            'user_id'    => $item->userId(),
            'user_type'  => $item->userType(),
            'owner_id'   => $item->ownerId(),
            'owner_type' => $item->ownerType(),
        ];

        //Support for item updated fields in $coreData
        if ($item instanceof Model) {
            $originalData = $coreData;

            if ($item->isDirty()) {
                if (!empty($item->getOriginal())) {
                    foreach ($item->getChanges() as $field => $value) {
                        /** @var string $field */
                        if (array_key_exists($field, $originalData) && array_key_exists($field, $item->getOriginal())) {
                            $originalData[$field] = $item->getOriginal($field);
                        }
                    }
                }
            }

            //Trigger observer
            $old = $this->getModel()->newQuery()
                ->where($originalData)
                ->first();

            if ($old instanceof Search) {
                $old->delete();
            }
        }

        $data = array_merge($data, [
            'privacy' => MetaFoxPrivacy::EVERYONE,
        ]);

        $privacyIds = [MetaFoxPrivacy::NETWORK_PUBLIC_PRIVACY_ID];

        if ($item instanceof HasPrivacy) {
            Arr::set($data, 'privacy', $item->privacy);

            $privacyIds = $this->getPrivacyIds($item);
        }

        if (!is_array($privacyIds)) {
            return;
        }

        $model = $this->updateOrCreate($coreData, $data);

        if (!$model instanceof Search) {
            return;
        }

        foreach ($privacyIds as $privacyId) {
            $this->createPrivacyStream($model->entityId(), $privacyId);
        }

        $this->handleTagData($model, $data);
    }

    public function deletedBy(HasGlobalSearch $item): void
    {
        // validate is item
        if (!$item instanceof Content) {
            return;
        }

        $data = $item->toSearchable();

        if (!$data) {
            return;
        }

        $coreData = [
            'item_id'    => $item->entityId(),
            'item_type'  => $item->entityType(),
            'user_id'    => $item->userId(),
            'user_type'  => $item->userType(),
            'owner_id'   => $item->ownerId(),
            'owner_type' => $item->ownerType(),
        ];

        $this->deleteWhere($coreData);
    }

    public function privacyMemberRepository(): PrivacyMemberRepositoryInterface
    {
        return resolve(PrivacyMemberRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function searchItems(User $context, array $params): array
    {
        $limit = $params['limit'] ?? Pagination::DEFAULT_ITEM_PER_PAGE;

        $streamManager = $this->getStreamManager();

        $streamManager
            ->setUser($context)
            ->setLimit($limit)
            ->setAttributes($params);

        if (Arr::has($params, 'q')) {
            $streamManager->setSearchText($params['q']);
        }

        if (Arr::has($params, 'view')) {
            $streamManager->setView($params['view']);
        }

        return $streamManager->fetchStreamContinuous();
    }

    /**
     * @param  User                                     $user
     * @param  array<string, mixed>                     $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSuggestion(User $user, array $params = []): Collection
    {
        if ($params['is_hashtag_search']) {
            return resolve(TagRepositoryInterface::class)->searchHashtags($user, $params);
        }

        return $this->searchUsers($user, $params);
    }

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $params
     * @return Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function searchUsers(User $context, array $params): Collection
    {
        $q = Arr::get($params, 'q', MetaFoxConstant::EMPTY_STRING);

        $limit = Arr::get($params, 'limit', 5);

        $query = $this->getModel()->newQuery()
            ->with(['item'])
            ->join('user_entities', function (JoinClause $joinClause) {
                $joinClause->on('user_entities.id', '=', 'search_items.item_id')
                    ->on('user_entities.entity_type', '=', 'search_items.item_type')
                    ->where('user_entities.is_searchable', '=', 1);
            });

        if (MetaFoxConstant::EMPTY_STRING !== $q) {
            $query->terms($q);
        }

        $this->addBlockedScope($query, $this->getModel()->newModelInstance(), $context);

        return $query
            ->orderByDesc('user_entities.is_featured')
            ->orderBy(DB::raw('CASE user_entities.entity_type WHEN \'user\' THEN 1 ELSE 0 END'), 'desc')
            ->orderByDesc('user_entities.id')
            ->limit($limit)
            ->get(['search_items.*'])
            ->map(function ($item) {
                return $item->item;
            });
    }

    protected function addBlockedScope(Builder $builder, Model $model, User $context): void
    {
        $resourceUserColumn = $model->getTable() . '.user_id';

        $resourceOwnerColumn = $model->getTable() . '.owner_id';

        // Resources post by blocked users.
        $builder->leftJoin('user_blocked as blocked_owner', function (JoinClause $join) use ($resourceUserColumn, $context) {
            $join->on('blocked_owner.owner_id', '=', $resourceUserColumn)
                ->where('blocked_owner.user_id', '=', $context->entityId());
        })->whereNull('blocked_owner.owner_id');

        // Resources post by users blocked you.
        $builder->leftJoin('user_blocked as blocked_user', function (JoinClause $join) use ($resourceUserColumn, $context) {
            $join->on('blocked_user.user_id', '=', $resourceUserColumn)
                ->where('blocked_user.owner_id', '=', $context->entityId());
        })->whereNull('blocked_user.user_id');

        // Resources post on users blocked you.
        $builder->leftJoin(
            'user_blocked as blocked_on_user',
            function (JoinClause $join) use ($resourceOwnerColumn, $context) {
                $join->on('blocked_on_user.user_id', '=', $resourceOwnerColumn)
                    ->where('blocked_on_user.owner_id', '=', $context->entityId());
            }
        )->whereNull('blocked_on_user.user_id');
    }

    protected function createPrivacyStream(int $id, int $privacyId): ?Model
    {
        return PrivacyStream::query()
            ->firstOrCreate([
                'item_id'    => $id,
                'privacy_id' => $privacyId,
            ]);
    }

    public function getGroups(User $context, array $attributes = []): Collection
    {
        Arr::set($attributes, 'limit', null);

        $query = $this->buildQuery($context, $attributes);

        $resolution = MetaFox::getResolution();

        $viewContext = Arr::get($attributes, 'view_context', Support::VIEW_SEARCH);

        $searchMenuName = match ($resolution) {
            MetaFoxConstant::RESOLUTION_MOBILE => $this->getMobileSearchMenuName($viewContext),
            MetaFoxConstant::RESOLUTION_WEB    => $this->getWebSearchMenuName($viewContext),
        };

        if (null === $searchMenuName) {
            return collect([]);
        }

        $menuItems = resolve(MenuItemRepositoryInterface::class)->getMenuItemByMenuName(
            $searchMenuName,
            $resolution,
            true
        );

        $menuItems = $menuItems
            ->filter(function ($item) {
                return $item->name != Browse::VIEW_ALL;
            })
            ->pluck('name')
            ->toArray();

        if (!count($menuItems)) {
            return collect([]);
        }

        $groups = $query
            ->select(['search_items.item_type', DB::raw('COUNT(*) as total_item')])
            ->whereIn('search_items.item_type', $menuItems)
            ->groupBy('search_items.item_type')
            ->get()
            ->map(function ($searchItem) {
                $label = null;

                $alias = getAliasByEntityType($searchItem->itemType());

                if (null !== $alias) {
                    $label = __p($alias . '::phrase.' . $searchItem->itemType() . '_global_search_label');
                }

                $data = $searchItem->toArray();

                return array_merge($data, [
                    'item_module_name' => $alias,
                    'label'            => $label,
                ]);
            })
            ->toArray();

        if (!count($groups)) {
            return collect([]);
        }

        $orderingMenuName = $this->getSearchOrderingMenuName($resolution);

        $orderingMenus = resolve(MenuItemRepositoryInterface::class)->getMenuItemByMenuName(
            $orderingMenuName,
            $resolution,
            true
        );

        if (!$orderingMenus->count()) {
            return collect($groups);
        }

        $collection = new Collection();

        $groups = array_combine(array_column($groups, 'item_type'), $groups);

        foreach ($orderingMenus as $orderingMenu) {
            $name = $orderingMenu->name;

            if (null === $name) {
                continue;
            }

            if (!Arr::has($groups, $name)) {
                continue;
            }

            $collection->push(Arr::get($groups, $name));
        }

        return $collection;
    }

    protected function getSearchOrderingMenuName(string $resolution): string
    {
        //@TODO: support menu for mobile if need
        return 'search.webCategoryOrderingMenu';
    }

    protected function getMobileSearchMenuName(string $view = 'search'): ?string
    {
        $prefix = 'search.';

        return match ($view) {
            Support::VIEW_SEARCH  => $prefix . 'mobileCategoryMenu',
            Support::VIEW_HASHTAG => $prefix . 'mobileHashtagCategoryMenu',
            default               => null,
        };
    }

    protected function getWebSearchMenuName(string $view): ?string
    {
        $prefix = 'search.';

        return match ($view) {
            Support::VIEW_SEARCH  => $prefix . 'webCategoryMenu',
            Support::VIEW_HASHTAG => $prefix . 'webHashtagCategoryMenu',
            default               => null,
        };
    }

    public function buildQuery(User $context, array $attributes = []): Builder
    {
        $q = Arr::get($attributes, 'q', MetaFoxConstant::EMPTY_STRING);

        $from = Arr::get($attributes, 'from', Browse::VIEW_ALL);

        $friendOnly = Arr::get($attributes, 'related_comment_friend_only', false);

        $isHashtag = Arr::get($attributes, 'view_context', Support::VIEW_SEARCH) == Support::VIEW_HASHTAG;

        $ownerId = Arr::get($attributes, 'owner_id', 0);

        $query = $this->getModel()->newQuery();

        $this->buildQueryForHashtag($query, $isHashtag, $q);

        $this->buildQueryForOwner($query, $from);

        $this->buildQueryForPrivacy($context, $query, $ownerId, $friendOnly);

        $this->buildQueryForOverriden($context, $query, $attributes);

        $this->buildQueryForWhen($query, $attributes);

        return $query;
    }

    protected function buildQueryForWhen(Builder $query, array $attributes): void
    {
        $when = Arr::get($attributes, 'when', Browse::VIEW_ALL);

        $scope = new WhenScope($when);

        $scope->setWhenColumn('created_at');

        $query->addScope($scope);
    }

    protected function buildQueryForOverriden(User $context, Builder $query, array $attributes): void
    {
        $view = Arr::get($attributes, 'view');

        $lastSearchId = Arr::get($attributes, 'last_search_id', 0);

        $limit = Arr::get($attributes, 'limit', Pagination::DEFAULT_ITEM_PER_PAGE);

        $overriden = false;

        if (null !== $view) {
            $query->where('search_items.item_type', '=', $view);

            $overriden = app('events')->dispatch('search.builder.override', [$view, $context, $attributes], true);
        }

        if (false === $overriden) {
            if ($lastSearchId > 0) {
                $query->where('search_items.id', '<', $lastSearchId);
            }

            if (null !== $limit) {
                $query->limit($limit);
            }
        }
    }

    protected function buildQueryForHashtag(Builder $query, bool $isHashtag, ?string $q): void
    {
        if (MetaFoxConstant::EMPTY_STRING !== $q) {
            match ($isHashtag) {
                true  => $this->handleSearchHashtag($query, $q),
                false => $this->handleSearchText($query, $q),
            };
        }
    }

    protected function buildQueryForOwner(Builder $query, string $from): void
    {
        if ($from != Browse::VIEW_ALL) {
            $query->where('search_items.owner_type', '=', $from);
        }
    }

    protected function buildQueryForPrivacy(User $context, Builder $query, int $ownerId, bool $friendOnly): void
    {
        match ($friendOnly) {
            true  => $this->handleFriendPrivacy($query, $context),
            false => $this->handlePrivacy($query, $context, $ownerId),
        };
    }

    protected function handlePrivacy(Builder $builder, User $context, int $ownerId = 0): void
    {
        // Browse by item's privacy
        $scope = new PrivacyScope();

        $scope->setUserId($context->entityId());

        $scope->setModerationUserRoles([UserRole::SUPER_ADMIN_USER]);

        if ($ownerId > 0) {
            $userEntity = UserEntityFacade::getById($ownerId);

            //Ignore owner from search items
            if ($userEntity instanceof UserEntity) {
                $builder->where(function (Builder $builder) use ($userEntity) {
                    $builder->where('search_items.item_type', '<>', $userEntity->entityType())
                        ->orWhere('search_items.item_id', '<>', $userEntity->entityId());
                });
            }

            $scope->setOwnerId($ownerId);
        }

        $builder->addScope($scope);
    }

    protected function handleFriendPrivacy(Builder $builder, User $context): void
    {
        if (!app_active('metafox/friend')) {
            return;
        }

        $builder->join('friends', function (JoinClause $join) use ($context) {
            $join->on('search_items.user_id', '=', 'friends.owner_id');
            $join->where('friends.user_id', $context->entityId());
        });
    }

    protected function handleSearchText(Builder $builder, string $q): void
    {
        $builder->terms($q);
    }

    protected function handleSearchHashtag(Builder $builder, string $q): void
    {
        $tagScope = new TagScope($q);

        $builder->addScope($tagScope);
    }

    public function updateSearchText(string $itemType, int $itemId, array $attributes): bool
    {
        $model = $this->getModel()->newQuery()
            ->where([
                'item_type' => $itemType,
                'item_id'   => $itemId,
            ])
            ->first();

        if (!$model instanceof Search) {
            return false;
        }

        if (!$model->update($attributes)) {
            return false;
        }

        $this->handleTagData($model, $attributes);

        return true;
    }

    public function getWhenOptions(): array
    {
        return [
            ['label' => __p('core::phrase.when.all'), 'value' => Browse::WHEN_ALL],
            ['label' => __p('core::phrase.when.this_month'), 'value' => Browse::WHEN_THIS_MONTH],
            ['label' => __p('core::phrase.when.this_week'), 'value' => Browse::WHEN_THIS_WEEK],
            ['label' => __p('core::phrase.when.today'), 'value' => Browse::WHEN_TODAY],
        ];
    }

    public function deletedByItem(string $itemType, int $itemId): bool
    {
        $model = $this->getModel()->newQuery()
            ->where([
                'item_type' => $itemType,
                'item_id'   => $itemId,
            ])
            ->first();

        if (!$model instanceof Search) {
            return false;
        }

        $model->delete();

        return true;
    }
}
