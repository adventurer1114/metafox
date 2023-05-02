<?php

namespace MetaFox\Page\Repositories\Eloquent;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Models\PageClaim;
use MetaFox\Page\Models\PageMember;
use MetaFox\Page\Policies\CategoryPolicy;
use MetaFox\Page\Policies\PagePolicy;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Page\Repositories\PageInviteRepositoryInterface;
use MetaFox\Page\Repositories\PageMemberRepositoryInterface;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Page\Support\Browse\Scopes\Page\BlockedScope;
use MetaFox\Page\Support\Browse\Scopes\Page\SortScope;
use MetaFox\Page\Support\Browse\Scopes\Page\ViewScope;
use MetaFox\Page\Support\CacheManager;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\CategoryScope;
use MetaFox\Platform\Support\Browse\Scopes\PrivacyScope;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Browse\Scopes\SortScope as SortScopeSupport;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;
use MetaFox\Platform\Support\Repository\HasApprove;
use MetaFox\Platform\Support\Repository\HasFeatured;
use MetaFox\Platform\Support\Repository\HasSponsor;
use MetaFox\Platform\UserRole;

/**
 * Class PageRepository.
 * @method Page getModel()
 * @method Page find($id, $columns = ['*'])
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class PageRepository extends AbstractRepository implements PageRepositoryInterface
{
    use HasSponsor;
    use HasFeatured;
    use HasApprove;
    use CollectTotalItemStatTrait;

    public function model(): string
    {
        return Page::class;
    }

    public function memberRepository(): PageMemberRepositoryInterface
    {
        return resolve(PageMemberRepositoryInterface::class);
    }

    public function inviteRepository(): PageInviteRepositoryInterface
    {
        return resolve(PageInviteRepositoryInterface::class);
    }

    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes
     *
     * @return EloquentBuilder
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function buildQueryViewPages(User $context, User $owner, array $attributes): EloquentBuilder
    {
        $sort       = $attributes['sort'] ?? SortScopeSupport::SORT_DEFAULT;
        $sortType   = $attributes['sort_type'] ?? SortScopeSupport::SORT_TYPE_DEFAULT;
        $when       = $attributes['when'] ?? Browse::WHEN_ALL;
        $view       = $attributes['view'] ?? Browse::VIEW_ALL;
        $search     = $attributes['q'] ?? null;
        $categoryId = $attributes['category_id'] ?? null;

        // Scopes.
        $privacyScope = new PrivacyScope();
        $privacyScope->setUserId($context->entityId());

        $sortScope = new SortScope();
        $sortScope->setSort($sort)->setSortType($sortType);

        $whenScope = new WhenScope();
        $whenScope->setWhen($when);

        $viewScope = new ViewScope();
        $viewScope->setUserContext($context)->setView($view);

        $blockedScope = new BlockedScope();
        $blockedScope->setContextId($context->entityId());
        $query = $this->getModel()->newQuery()
            ->with(['userEntity']);

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['name']));
        }

        if ($categoryId > 0) {
            if (!is_array($categoryId)) {
                $categoryId = [$categoryId];
            }

            $categoryScope = new CategoryScope();
            $categoryScope->setCategories($categoryId);
            $query->addScope($categoryScope);
        }

        if ($owner->entityId() != $context->entityId()) {
            $query->where('pages.user_id', '=', $owner->entityId())
                ->where('pages.is_approved', 1);

            $viewScope->setIsViewProfile(true);
        }

        return $query->addScope($privacyScope)
            ->addScope($sortScope)
            ->addScope($whenScope)
            ->addScope($blockedScope)
            ->addScope($viewScope);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function viewPages(User $context, User $owner, array $attributes): Paginator
    {
        policy_authorize(PagePolicy::class, 'viewAny', $context);

        $view      = $attributes['view'];
        $limit     = $attributes['limit'];
        $profileId = $attributes['user_id'];

        if ($profileId > 0 && $profileId == $context->entityId()) {
            $attributes['view'] = $view = Browse::VIEW_MY;
        }

        if ($view == Browse::VIEW_FEATURE) {
            return $this->findFeature($limit);
        }

        if ($view == Browse::VIEW_SPONSOR) {
            return $this->findSponsor($limit);
        }

        if (Browse::VIEW_PENDING == $view) {
            if (Arr::get($attributes, 'user_id') == 0) {
                if ($context->isGuest() || !$context->hasPermissionTo('page.approve')) {
                    throw new AuthorizationException(__p('core::validation.this_action_is_unauthorized'), 403);
                }
            }
        }
        $categoryId = Arr::get($attributes, 'category_id', 0);

        if ($categoryId > 0) {
            $category = resolve(PageCategoryRepositoryInterface::class)->find($categoryId);

            policy_authorize(CategoryPolicy::class, 'viewActive', $context, $category);
        }

        $query = $this->buildQueryViewPages($context, $owner, $attributes)
            ->with(['userEntity']);

        $pageData = $query->simplePaginate($limit, ['pages.*']);

        $attributes['current_page'] = $pageData->currentPage();
        //Load sponsor on first page only
        if (!$this->hasSponsorView($attributes)) {
            return $pageData;
        }

        $userId = $context->entityId();

        $cacheKey  = sprintf(CacheManager::SPONSOR_ITEM_CACHE, $userId);
        $cacheTime = CacheManager::SPONSOR_ITEM_CACHE_ITEM;

        return $this->transformPaginatorWithSponsor($pageData, $cacheKey, $cacheTime);
    }

    public function viewPage(User $context, int $id): Page
    {
        $page = $this->find($id);

        policy_authorize(PagePolicy::class, 'view', $context, $page);

        $page->with(['type', 'category', 'userEntity', 'pageText']);

        return $page;
    }

    public function createPage(User $context, array $attributes): Page
    {
        policy_authorize(PagePolicy::class, 'create', $context);

        $attributes = array_merge($attributes, [
            'user_id'     => $context->entityId(),
            'user_type'   => $context->entityType(),
            'privacy'     => MetaFoxPrivacy::EVERYONE,
            'is_approved' => (int) $context->hasPermissionTo('page.auto_approved'),
        ]);

        /** @var Page $page */
        $page = parent::create($attributes);

        $this->memberRepository()->addPageMember($page, $context->entityId(), PageMember::ADMIN);

        $page->assignRole(UserRole::PAGE_USER);

        if (!empty($attributes['user_ids'])) {
            $this->inviteRepository()->inviteFriends($context, $page->entityId(), $attributes['user_ids']);
        }

        $page->refresh();

        $page->with(['category', 'userEntity', 'pageText']);

        return $page;
    }

    public function updatePage(User $context, int $id, array $attributes): Page
    {
        $page = $this->find($id);
        policy_authorize(PagePolicy::class, 'update', $context, $page);

        $page->update($attributes);
        $page->refresh();

        return $page;
    }

    public function deletePage(User $context, int $id): bool
    {
        $page = $this->find($id);

        policy_authorize(PagePolicy::class, 'delete', $context, $page);

        /*
         * Please move this dispatch to forceDelete when implementing soft delete if need
         */
        app('events')->dispatch('user.deleting', [$page]);

        $page->delete();

        /*
         * Please move this dispatch to forceDelete when implementing soft delete if need
         */
        app('events')->dispatch('user.deleted', [$page]);

        return true;
    }

    public function updateAvatar(User $context, int $id, ?UploadedFile $image, string $imageCrop): array
    {
        $page = $this->find($id);
        policy_authorize(PagePolicy::class, 'update', $context, $page);

        if (null == $image) {
            if (null == $page->avatar_file_id) {
                throw ValidationException::withMessages([
                    __p('validation.required', ['attribute' => 'image']),
                ]);
            }
        }

        if (null != $image) {
            $params = [
                'privacy' => $page->privacy,
                'path'    => 'page',
                'files'   => [
                    [
                        'file' => $image,
                    ],
                ],
            ];

            $photos = $this->createPhoto($context, $page, $params, 1, Page::PAGE_UPDATE_PROFILE_ENTITY_TYPE);

            if (empty($photos)) {
                abort(400, __('validation.something_went_wrong_please_try_again'));
            }

            $photos = $photos->toArray();

            $page->update([
                'avatar_id'      => $photos[0]['id'],
                'avatar_type'    => 'photo',
                'avatar_file_id' => $photos[0]['image_file_id'],
            ]);
        }

        $page->refresh();

        $uploadFile = upload()->convertBase64ToUploadedFile($imageCrop);

        $uploadedImage = upload()
            ->setThumbSizes(['50x50', '120x120', '200x200'])
            ->setPath('page')
            ->storeFile($uploadFile);

        $page->update(['avatar_file_id' => $uploadedImage->id]);

        $feedId = 0;

        $itemId = $page->getAvatarId();

        $itemType = $page->getAvatarType();

        try {
            /** @var Content $feed */
            $feed = app('events')->dispatch(
                'activity.get_feed_by_item_id',
                [$context, $itemId, $itemType, $itemType],
                true
            );
            $feedId = $feed->entityId();

            if (null == $image) {
                app('events')->dispatch('activity.push_feed_on_top', [$feedId], true);
            }
        } catch (Exception $e) {
            // Silent.
            Log::error($e->getMessage());
        }

        return [
            'user'       => $page->refresh(),
            'feed_id'    => $feedId,
            'is_pending' => false, //Todo check setting
        ];
    }

    public function updateCover(User $context, int $id, array $attributes): array
    {
        $page = $this->find($id);

        policy_authorize(PagePolicy::class, 'update', $context, $page);

        $coverData    = [];
        $positionData = [];
        $feedId       = 0;

        if (isset($attributes['position'])) {
            $positionData['cover_photo_position'] = $attributes['position'];
        }

        if (isset($attributes['image'])) {
            policy_authorize(PagePolicy::class, 'uploadCover', $context, $page);

            $params = [
                'privacy'         => $page->privacy,
                'path'            => 'page',
                'thumbnail_sizes' => $page->getCoverSizes(),
                'files'           => [
                    [
                        'file' => $attributes['image'],
                    ],
                ],
            ];

            /** @var Collection $photos */
            $photos = $this->createPhoto($context, $page, $params, 2, Page::PAGE_UPDATE_COVER_ENTITY_TYPE);

            if (empty($photos)) {
                abort(400, __('validation.something_went_wrong_please_try_again'));
            }

            foreach ($photos as $photo) {
                $photo->toArray();
                $coverData = [
                    'cover_id'             => $photo['id'],
                    'cover_type'           => 'photo',
                    'cover_file_id'        => $photo['image_file_id'],
                    'cover_photo_position' => null,
                ];

                break;
            }
            unset($attributes['image']);
        }
        $page->update(array_merge($attributes, $coverData, $positionData));

        $page->refresh()->with('user');

        // $page->cover;//get photo -> feed

        return [
            'user'       => $page,
            'feed_id'    => $feedId,
            'is_pending' => false, //Todo check setting
        ];
    }

    public function removeCover(User $context, int $id): bool
    {
        $page = $this->find($id);

        policy_authorize(PagePolicy::class, 'editCover', $context, $page);

        return $page->update($page->getCoverDataEmpty());
    }

    public function findFeature(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_featured', 1)
            ->where('is_approved', 1)
            ->orderByDesc('featured_at')
            ->simplePaginate($limit);
    }

    public function findSponsor(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_sponsor', 1)
            ->where('is_approved', 1)
            ->simplePaginate($limit);
    }

    /**
     * @param  User                 $context
     * @param  User                 $owner
     * @param  array<string, mixed> $params
     * @param  int                  $type
     * @param  string|null          $feedType
     * @return Collection|null
     */
    protected function createPhoto(
        User $context,
        User $owner,
        array $params,
        int $type,
        ?string $feedType = null
    ): ?Collection {
        /** @var Collection $photos */
        $photos = app('events')->dispatch('photo.create', [$context, $owner, $params, $type, $feedType], true);

        return $photos;
    }

    public function claimPage(User $user, int $id, ?string $message = null): bool
    {
        $page    = $this->with(['pageClaim'])->find($id);
        $adminId = Settings::get('page.admin_in_charge_of_page_claims', 0);

        if ($adminId == 0) {
            abort(400, __p('page::phrase.no_admin_has_been_set_to_handle_this_type_of_issues'));
        }

        policy_authorize(PagePolicy::class, 'claim', $user, $page);

        return (new PageClaim([
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
            'page_id'   => $page->entityId(),
            'message'   => $message,
        ]))->save();
    }

    /**
     * @inerhitDoc
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @throws AuthorizationException
     */
    public function getSuggestion(User $context, array $params = [], bool $getEnoughLimit = true): array
    {
        if (!app_active('metafox/friend')) {
            return [];
        }

        $query = DB::table('friends as f')
            ->select('pages.*')
            ->join('core_privacy_members as member', function (JoinClause $join) use ($context) {
                $join->on('member.user_id', '=', 'f.owner_id');
                $join->where('member.user_id', '!=', $context->entityId());
            })
            ->rightJoin('core_privacy as privacy', function (JoinClause $join) {
                $join->on('privacy.privacy_id', '=', 'member.privacy_id');

                $join->where('privacy.item_type', '=', Page::ENTITY_TYPE);
                $join->where('privacy.privacy', '=', MetaFoxPrivacy::FRIENDS);
                $join->where('privacy.privacy_type', '=', Page::PAGE_MEMBERS);
            })
            ->rightJoin('pages', function (JoinClause $join) {
                $join->on('pages.id', '=', 'privacy.item_id');
            })
            ->leftJoin('core_privacy_members as our_member', function (JoinClause $join) use ($context) {
                $join->on('our_member.privacy_id', '=', 'privacy.privacy_id');
                $join->where('our_member.user_id', '=', $context->entityId());
            })
            ->where('f.user_id', '=', $context->entityId())
            ->whereNull('our_member.user_id');

        $limit = 3;

        if (isset($params['limit'])) {
            $limit = $params['limit'];
        }

        $query->limit($limit);

        $query->orderBy('pages.id', 'DESC');

        $data = $query->get();

        $suggestPages = [];

        if ($data->count() > 0) {
            $suggestPages = $this->convertToPage($data);
        }

        if ($data->count() >= $limit) {
            return $suggestPages;
        }

        if ($getEnoughLimit === false) {
            return $suggestPages;
        }

        $suggestPageIds = array_keys($suggestPages);

        $morePageParams = [
            'limit'       => $limit - $data->count(),
            'not_in_ids'  => $suggestPageIds,
            'view'        => ViewScope::VIEW_DEFAULT,
            'sort'        => SortScopeSupport::SORT_DEFAULT,
            'sort_type'   => SortScopeSupport::SORT_TYPE_DEFAULT,
            'when'        => WhenScope::WHEN_DEFAULT,
            'type_id'     => 0,
            'category_id' => $params['category_id'] ?? 0,
            'q'           => '',
            'user_id'     => 0,
        ];

        $morePages = $this->viewPages($context, $context, $morePageParams)->items();

        foreach ($morePages as $morePage) {
            $morePages[] = $morePage;
        }

        return $morePages;
    }

    /**
     * @param Collection $collection
     *
     * @return array
     */
    private function convertToPage(Collection $collection): array
    {
        $result = [];

        foreach ($collection as $item) {
            $json = json_encode($item);
            if (!$json) {
                continue;
            }
            $data = json_decode($json, true);

            $page = new Page();
            $page->forceFill($data);
            $result[] = $page;
        }

        return $result;
    }

    public function getPageForMention(User $context, array $attributes): Paginator
    {
        $search = $attributes['q'];
        $limit  = $attributes['limit'];

        $query = $this->getModel()->newQuery()
            ->join('page_members AS pm', function (JoinClause $join) use ($context) {
                $join->on('pm.page_id', '=', 'pages.id')
                    ->where('pm.user_id', '=', $context->entityId())
                    ->where('pages.is_approved', '=', 1);
            });

        if ('' != $search) {
            $query->orWhere('pages.name', $this->likeOperator(), $search . '%');
        }

        return $query->simplePaginate($limit, ['pages.*']);
    }

    public function getPageBuilder(User $user): Builder
    {
        $builder = DB::table('user_entities');

        $builder->select('user_entities.id')
            ->where('user_entities.entity_type', '=', Page::ENTITY_TYPE);

        $builder->join('pages', function (JoinClause $joinClause) {
            $joinClause->on('pages.id', '=', 'user_entities.id')
                ->where('pages.is_approved', '=', 1);
        });

        $builder->leftJoin('user_blocked as blocked_owner', function (JoinClause $join) use ($user) {
            $join->on('blocked_owner.owner_id', '=', 'user_entities.id')
                ->where('blocked_owner.user_id', '=', $user->entityId());
        })->whereNull('blocked_owner.owner_id');

        // Resources post by users blocked you.
        $builder->leftJoin('user_blocked as blocked_user', function (JoinClause $join) use ($user) {
            $join->on('blocked_user.user_id', '=', 'user_entities.id')
                ->where('blocked_user.owner_id', '=', $user->entityId());
        })->whereNull('blocked_user.user_id');

        return $builder;
    }

    /**
     * @throws AuthorizationException
     */
    public function viewSimilar(User $context, array $attributes): Paginator
    {
        policy_authorize(PagePolicy::class, 'viewAny', $context);

        $pageId     = $attributes['page_id'] ?? null;
        $limit      = $attributes['limit'] ?? 3;
        $categoryId = $attributes['category_id'] ?? null;
        $contextId  = $context->entityId();

        $pageQuery = $this->getModel()->newQuery()->select('pages.*')
            ->where('pages.is_approved', 1);

        if (isset($pageId)) {
            $page       = $this->find($pageId);
            $categoryId = $page->category_id;

            $pageQuery->whereKeyNot($pageId);
        }

        // friend pages
        $friendPagesQuery = $this->buildQueryViewPages($context, $context, [
            'view' => ViewScope::VIEW_FRIEND_MEMBER,
        ])->select('pages.id')->groupBy('pages.id');

        $pageQuery->leftJoinSub($friendPagesQuery->getQuery(), 'friend_pages', function (JoinClause $join) {
            $join->on('friend_pages.id', '=', 'pages.id');
        });

        // not liked pages
        $pageQuery->leftJoin('page_members AS pm', function (JoinClause $join) use ($contextId) {
            $join->on('pm.page_id', '=', 'pages.id')
                ->where('pm.user_id', $contextId);
        })->whereNull('pm.page_id');

        $pageQuery->where(function (EloquentBuilder $subQuery) use ($categoryId) {
            $subQuery->whereNotNull('friend_pages.id');

            if (isset($categoryId)) {
                $subQuery->orWhere('pages.category_id', '=', $categoryId);
            }
        });

        $pageQuery->orderByRaw('CASE WHEN friend_pages.id IS NOT NULL THEN 1 ELSE 0 END ' . Browse::SORT_TYPE_DESC);
        if (isset($categoryId)) {
            $pageQuery->orderByRaw('CASE pages.category_id WHEN ? THEN 1 ELSE 0 END ' . Browse::SORT_TYPE_DESC, [
                $categoryId,
            ]);
        }
        $pageQuery->orderBy('pages.id', Browse::SORT_TYPE_DESC);

        $privacyScope = new PrivacyScope();
        $privacyScope->setUserId($contextId);
        $pageQuery->addScope($privacyScope);

        return $pageQuery->simplePaginate($limit);
    }

    /**
     * @inheritDoc
     */
    public function deleteUserData(int $userId): void
    {
        $pages = $this->getModel()->newQuery()
            ->where([
                'user_id' => $userId,
            ])
            ->get();

        if ($pages->count()) {
            $pages->each(function ($page) {
                $page->delete();
            });
        }
    }
}
