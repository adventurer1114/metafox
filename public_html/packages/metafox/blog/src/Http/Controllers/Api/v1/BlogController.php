<?php

namespace MetaFox\Blog\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Blog\Http\Requests\v1\Blog\CreateFormRequest;
use MetaFox\Blog\Http\Requests\v1\Blog\IndexRequest;
use MetaFox\Blog\Http\Requests\v1\Blog\PatchRequest;
use MetaFox\Blog\Http\Requests\v1\Blog\StoreRequest;
use MetaFox\Blog\Http\Requests\v1\Blog\UpdateRequest;
use MetaFox\Blog\Http\Resources\v1\Blog\BlogDetail;
use MetaFox\Blog\Http\Resources\v1\Blog\BlogItemCollection;
use MetaFox\Blog\Http\Resources\v1\Blog\SearchBlogForm as SearchForm;
use MetaFox\Blog\Http\Resources\v1\Blog\StoreBlogForm;
use MetaFox\Blog\Http\Resources\v1\Blog\UpdateBlogForm;
use MetaFox\Blog\Models\Blog;
use MetaFox\Blog\Policies\BlogPolicy;
use MetaFox\Blog\Repositories\BlogRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorInFeedRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorRequest;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class BlogController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group blog
 */
class BlogController extends ApiController
{
    /**
     * @var BlogRepositoryInterface
     */
    private BlogRepositoryInterface $repository;

    /**
     * @param BlogRepositoryInterface $repository
     */
    public function __construct(BlogRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse blog.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $context = user();
        $owner   = $context;

        if ($params['user_id'] > 0) {
            $owner = UserEntity::getById($params['user_id'])->detail;

            if (!policy_check(BlogPolicy::class, 'viewOnProfilePage', $context, $owner)) {
                return $this->success([]);
            }

            if (!UserPrivacy::hasAccess($context, $owner, 'blog.profile_menu')) {
                return $this->success([]);
            }
        }

        $data = $this->repository->viewBlogs($context, $owner, $params);

        return $this->success(new BlogItemCollection($data));
    }

    /**
     * Create blog.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws PermissionDeniedException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $context = $owner = user();

        $params  = $request->validated();

        app('flood')->checkFloodControlWhenCreateItem(user(), Blog::ENTITY_TYPE);

        app('quota')->checkQuotaControlWhenCreateItem(user(), Blog::ENTITY_TYPE);

        if ($params['owner_id'] > 0) {
            if ($context->entityId() != $params['owner_id']) {
                $owner = UserEntity::getById($params['owner_id'])->detail;
            }
        }

        $blog = $this->repository->createBlog($context, $owner, $params);

        $message = __p('blog::phrase.blog_published_successfully');

        if (!$blog->isApproved()) {
            $message = __p('core::phrase.thanks_for_your_item_for_approval');
        }

        $ownerPendingMessage = $blog->getOwnerPendingMessage();

        if (null !== $ownerPendingMessage) {
            $message = $ownerPendingMessage;
        }

        if ($params['is_draft']) {
            $message = __p('blog::phrase.already_saved_blog_as_draft');
        }

        return $this->success(new BlogDetail($blog), [], $message);
    }

    /**
     * View Blog.
     *
     * @param int $id
     *
     * @return BlogDetail
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function show(int $id): BlogDetail
    {
        $blog = $this->repository->viewBlog(user(), $id);

        return new BlogDetail($blog);
    }

    /**
     * Update blog.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params   = $request->validated();
        $blog     = $this->repository->updateBlog(user(), $id, $params);
        $response = new BlogDetail($blog);
        $message  = __p('blog::phrase.blog_was_updated_successfully');

        $isPublished = true;
        if (isset($params['published'])) {
            $isPublished = $params['published'];
        }

        if (!$isPublished) {
            if (!$params['is_draft']) {
                $message = __p('blog::phrase.blog_published_successfully');
            }
        }

        return $this->success($response, [], $message);
    }

    /**
     * Delete blog.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function destroy(int $id): JsonResponse
    {
        $context = user();
        $this->repository->deleteBlog($context, $id);

        $message = __p('blog::phrase.blog_was_deleted_successfully');

        return $this->success([
            'id' => $id,
        ], [], $message);
    }

    /**
     * Patch update blog.
     *
     * @param  PatchRequest $request
     * @param  int          $id
     * @return JsonResponse
     */
    public function patch(PatchRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $blog   = $this->repository->find($id);

        return $this->success(new BlogDetail($blog));
    }

    /**
     * Sponsor blog.
     *
     * @param SponsorRequest $request
     * @param int            $id
     *
     * @return JsonResponse
     * @throws AuthenticationException|AuthorizationException
     */
    public function sponsor(SponsorRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $sponsor = $params['sponsor'];
        $this->repository->sponsor(user(), $id, $sponsor);

        $isSponsor = (bool) $sponsor;

        $message = $isSponsor ? 'core::phrase.resource_sponsored_successfully' : 'core::phrase.resource_unsponsored_successfully';
        $message = __p($message, ['resource_name' => __p('blog::phrase.blog')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }

    /**
     * Feature blog.
     *
     * @param FeatureRequest $request
     * @param int            $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function feature(FeatureRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $feature = $params['feature'];
        $this->repository->feature(user(), $id, $feature);

        $message = __p('blog::phrase.blog_featured_successfully');
        if (!$feature) {
            $message = __p('blog::phrase.blog_unfeatured_successfully');
        }

        return $this->success([
            'id'          => $id,
            'is_featured' => (int) $feature,
        ], [], $message);
    }

    /**
     * Approve blog.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function approve(int $id): JsonResponse
    {
        $resource = $this->repository->approve(user(), $id);

        // @todo recheck response.
        return $this->success(new BlogDetail($resource), [], __p('blog::phrase.blog_has_been_approved'));
    }

    /**
     * Publish blog.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function publish(int $id): JsonResponse
    {
        $context = user();
        $blog    = $this->repository->publish($context, $id);

        $message = $blog->isApproved()
            ? __p('blog::phrase.blog_published_successfully')
            : __p('blog::phrase.thank_you_for_your_item_it_s_been_submitted_to_admins_for_approval');

        return $this->success(new BlogDetail($blog), [], $message);
    }

    /**
     * @param CreateFormRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function formStore(CreateFormRequest $request): JsonResponse
    {
        $blog    = new Blog();
        $context = user();

        $data           = $request->validated();
        $blog->owner_id = $data['owner_id'];

        policy_authorize(BlogPolicy::class, 'create', $context);

        return $this->success(new StoreBlogForm($blog), [], '');
    }

    /**
     * @param CreateFormRequest $request
     * @param int               $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function formUpdate(CreateFormRequest $request, int $id): JsonResponse
    {
        $context = user();

        $blog = $this->repository->find($id);
        policy_authorize(BlogPolicy::class, 'update', $context, $blog);

        return $this->success(new UpdateBlogForm($blog), [], '');
    }

    /**
     * Sponsor blog in feed.
     *
     * @param SponsorInFeedRequest $request
     * @param int                  $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function sponsorInFeed(SponsorInFeedRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $sponsor = $params['sponsor'];
        $this->repository->sponsorInFeed(user(), $id, $sponsor);

        $isSponsor = (bool) $sponsor;

        $message = $isSponsor ? 'core::phrase.resource_sponsored_in_feed_successfully' : 'core::phrase.resource_unsponsored_in_feed_successfully';
        $message = __p($message, ['resource_name' => __p('blog::phrase.blog')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }

    /**
     * Get search form.
     *
     * @return AbstractForm
     * @todo Need working with policy + repository later
     */
    public function searchForm(): AbstractForm
    {
        return new SearchForm(null);
    }
}
