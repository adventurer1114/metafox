<?php

namespace MetaFox\Blog\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use MetaFox\Blog\Models\Blog;
use MetaFox\Blog\Policies\BlogPolicy;
use MetaFox\Blog\Policies\CategoryPolicy;
use MetaFox\Blog\Repositories\BlogRepositoryInterface;
use MetaFox\Blog\Repositories\CategoryRepositoryInterface;
use MetaFox\Blog\Support\Browse\Scopes\Blog\ViewScope;
use MetaFox\Blog\Support\CacheManager;
use MetaFox\Core\Repositories\AttachmentRepositoryInterface;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\CategoryScope;
use MetaFox\Platform\Support\Browse\Scopes\PrivacyScope;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;
use MetaFox\Platform\Support\Browse\Scopes\TagScope;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;
use MetaFox\Platform\Support\Repository\HasApprove;
use MetaFox\Platform\Support\Repository\HasFeatured;
use MetaFox\Platform\Support\Repository\HasSponsor;
use MetaFox\Platform\Support\Repository\HasSponsorInFeed;
use MetaFox\User\Traits\UserMorphTrait;

/**
 * Class BlogRepository.
 * @property Blog $model
 * @method   Blog getModel()
 * @method   Blog find($id, $columns = ['*'])
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @ignore
 * @codeCoverageIgnore
 */
class BlogRepository extends AbstractRepository implements BlogRepositoryInterface
{
    use HasSponsor;
    use HasFeatured;
    use HasApprove;
    use HasSponsorInFeed;
    use CollectTotalItemStatTrait;
    use UserMorphTrait;

    public function model(): string
    {
        return Blog::class;
    }

    public function createBlog(User $context, User $owner, array $attributes): Blog
    {
        policy_authorize(BlogPolicy::class, 'create', $context, $owner);

        $attributes = array_merge($attributes, [
            'user_id'    => $context->entityId(),
            'user_type'  => $context->entityType(),
            'owner_id'   => $owner->entityId(),
            'owner_type' => $owner->entityType(),
            'module_id'  => Blog::ENTITY_TYPE,
        ]);

        $attributes['title'] = $this->cleanTitle($attributes['title']);

        $attributes['image_file_id'] = upload()->getFileId($attributes['temp_file'], true);

        //only apply auto approve when $context == $owner
        if ($context->entityId() == $owner->entityId()) {
            if (!$context->hasPermissionTo('blog.auto_approved')) {
                $attributes['is_approved'] = 0;
            }
        }

        $blog = new Blog($attributes);

        if ($attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $blog->setPrivacyListAttribute($attributes['list']);
        }

        $blog->save();

        resolve(AttachmentRepositoryInterface::class)
            ->updateItemId($attributes['attachments'], $blog);

        $blog->refresh();

        return $blog;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function updateBlog(User $context, int $id, array $attributes): Blog
    {
        $blog = $this->find($id);

        $removeImage = Arr::get($attributes, 'remove_image', 0);

        policy_authorize(BlogPolicy::class, 'update', $context, $blog);

        if (isset($attributes['title'])) {
            $attributes['title'] = $this->cleanTitle($attributes['title']);
        }

        if ($removeImage) {
            $image = $blog->image_file_id;
            app('storage')->deleteFile($image, null);
            $attributes['image_file_id'] = null;
        }

        if ($attributes['temp_file'] > 0) {
            $attributes['image_file_id'] = upload()->getFileId($attributes['temp_file'], true);
        }

        $blog->fill($attributes);

        if (isset($attributes['privacy']) && $attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $blog->setPrivacyListAttribute($attributes['list']);
        }

        $blog->save();

        resolve(AttachmentRepositoryInterface::class)
            ->updateItemId($attributes['attachments'] ?? null, $blog);

        $blog->refresh();

        $this->updateFeedStatus($blog);

        return $blog;
    }

    protected function updateFeedStatus(Blog $blog): void
    {
        app('events')->dispatch('activity.feed.mark_as_pending', [$blog]);
    }

    public function deleteBlog(User $user, $id): int
    {
        $resource = $this->find($id);

        policy_authorize(BlogPolicy::class, 'delete', $user, $resource);

        return $this->delete($id);
    }

    public function viewBlogs(User $context, User $owner, array $attributes): Paginator
    {
        policy_authorize(BlogPolicy::class, 'viewAny', $context, $owner);

        $limit     = $attributes['limit'];
        $view      = $attributes['view'];
        $profileId = $attributes['user_id'];

        if ($view == Browse::VIEW_FEATURE) {
            return $this->findFeature($limit);
        }

        if ($view == Browse::VIEW_SPONSOR) {
            return $this->findSponsor($limit);
        }

        if ($profileId > 0 && $profileId == $context->entityId()) {
            $attributes['view'] = $view = Browse::VIEW_MY;
        }

        if (Browse::VIEW_PENDING == $view) {
            if (Arr::get($attributes, 'user_id') == 0) {
                if ($context->isGuest() || !$context->hasPermissionTo('blog.approve')) {
                    throw new AuthorizationException(__p('core::validation.this_action_is_unauthorized'), 403);
                }
            }
        }

        $categoryId = Arr::get($attributes, 'category_id', 0);

        if ($categoryId > 0) {
            $category = resolve(CategoryRepositoryInterface::class)->find($categoryId);

            policy_authorize(CategoryPolicy::class, 'viewActive', $context, $category);
        }

        $query = $this->buildQueryViewBlogs($context, $owner, $attributes);

        $relations = ['blogText', 'user', 'userEntity', 'activeCategories'];

        /** @var \Illuminate\Pagination\Paginator $blogData */
        $blogData = $query
            ->with($relations)
            ->simplePaginate($limit, ['blogs.*']);

        $attributes['current_page'] = $blogData->currentPage();
        //Load sponsor on first page only
        if (!$this->hasSponsorView($attributes)) {
            return $blogData;
        }

        $userId = $context->entityId();

        $cacheKey = sprintf(CacheManager::SPONSOR_ITEM_CACHE, $userId);

        $cacheTime = CacheManager::SPONSOR_ITEM_CACHE_TIME;

        return $this->transformPaginatorWithSponsor($blogData, $cacheKey, $cacheTime, 'id', $relations);
    }

    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes
     *
     * @return Builder
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function buildQueryViewBlogs(User $context, User $owner, array $attributes): Builder
    {
        $sort       = $attributes['sort'];
        $sortType   = $attributes['sort_type'];
        $when       = $attributes['when'] ?? '';
        $view       = $attributes['view'] ?? '';
        $search     = $attributes['q'] ?? '';
        $searchTag  = $attributes['tag'] ?? '';
        $categoryId = $attributes['category_id'];
        $profileId  = $attributes['user_id']; //$profileId == $owner->entityId() if has param user_id

        // Scopes.
        $privacyScope = new PrivacyScope();
        $privacyScope
            ->setUserId($context->entityId())
            ->setModerationPermissionName('blog.moderate');

        $sortScope = new SortScope($sort, $sortType);
        $whenScope = new WhenScope($when);

        $viewScope = new ViewScope();
        $viewScope->setUserContext($context)->setView($view)->setProfileId($profileId);

        $query = $this->getModel()->newQuery();

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['title']));
        }

        if ($searchTag != '') {
            $query = $query->addScope(new TagScope($searchTag));
        }

        if ($profileId > 0) {
            $query->where('blogs.is_draft', '!=', 1);
        }

        if ($owner->entityId() != $context->entityId()) {
            $privacyScope->setOwnerId($owner->entityId());

            $viewScope->setIsViewOwner(true);

            if (!policy_check(BlogPolicy::class, 'approve', $context, resolve(Blog::class))) {
                $query->where('blogs.is_approved', '=', 1);
            }
        }

        $hasCategorySearching = $categoryId > 0;

        match ($hasCategorySearching) {
            true  => $this->buildQueryForSearchingCategory($query, $categoryId),
            false => $this->buildQueryForSearching($query, $attributes),
        };

        $query = $this->applyDisplayBlogSetting($query, $owner, $view);

        return $query
            ->addScope($privacyScope)
            ->addScope($sortScope)
            ->addScope($whenScope)
            ->addScope($viewScope);
    }

    protected function buildQueryForSearchingCategory(Builder $query, mixed $categoryId): void
    {
        if (!is_array($categoryId)) {
            $categoryId = [$categoryId];
        }

        $categoryScope = new CategoryScope();

        $categoryScope->setCategories($categoryId);

        $query->addScope($categoryScope);
    }

    /**
     * @param  Builder              $query
     * @param  array<string, mixed> $attributes
     * @return void
     */
    protected function buildQueryForSearching(Builder $query, array $attributes): void
    {
        if (Arr::get($attributes, 'view') == Browse::VIEW_SEARCH) {
            $query->leftJoin('blog_category_data', function (JoinClause $joinClause) {
                $joinClause->on('blog_category_data.item_id', '=', 'blogs.id');
            })
                ->leftJoin('blog_categories', function (JoinClause $joinClause) {
                    $joinClause->on('blog_categories.id', '=', 'blog_category_data.category_id')
                        ->where('blog_categories.is_active', 1);
                });
        }
    }

    /**
     * @param  Builder $query
     * @param  User    $owner
     * @param  string  $view
     * @return Builder
     */
    private function applyDisplayBlogSetting(Builder $query, User $owner, string $view): Builder
    {
        if (in_array($view, [Browse::VIEW_MY, ViewScope::VIEW_DRAFT])) {
            return $query;
        }

        /*
         * Does not support view pending items from Group in My Pending Photos
         */
        if (!$owner instanceof HasPrivacyMember) {
            $query->where('blogs.owner_type', '=', $owner->entityType());
        }

        return $query;
    }

    public function viewBlog(User $context, int $id): Blog
    {
        $blog = $this
            ->with(['blogText', 'user', 'userEntity', 'categories', 'activeCategories', 'attachments'])
            ->find($id);

        policy_authorize(BlogPolicy::class, 'view', $context, $blog);

        if ($blog->isDraft() || $context->isGuest()) {
            return $blog->refresh();
        }

        $blog->incrementTotalView();
        $blog->refresh();

        return $blog;
    }

    public function findFeature(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_featured', Blog::IS_FEATURED)
            ->where('is_approved', Blog::IS_APPROVED)
            ->where('is_draft', '<>', 1)
            ->orderByDesc(HasFeature::FEATURED_AT_COLUMN)
            ->simplePaginate($limit);
    }

    public function findSponsor(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_sponsor', Blog::IS_SPONSOR)
            ->where('is_approved', Blog::IS_APPROVED)
            ->where('is_draft', '<>', 1)
            ->simplePaginate($limit);
    }

    public function publish(User $user, int $id): Blog
    {
        $blog = $this->find($id);

        policy_authorize(BlogPolicy::class, 'publish', $user, $blog);

        if (!$blog->isPublished()) {
            $blog->is_draft = 0;

            if (!$user->hasPermissionTo('blog.auto_approved')) {
                $blog->is_approved = 0;
            }

            $blog->save();
        }

        return $blog->refresh();
    }

    public function getTotalBlogsCount(): int
    {
        return $this->getModel()->newModelQuery()->count();
    }
}
