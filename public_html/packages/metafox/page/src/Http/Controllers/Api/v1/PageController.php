<?php

namespace MetaFox\Page\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use MetaFox\Page\Http\Requests\v1\Page\ClaimFormRequest;
use MetaFox\Page\Http\Requests\v1\Page\IndexRequest;
use MetaFox\Page\Http\Requests\v1\Page\MentionRequest;
use MetaFox\Page\Http\Requests\v1\Page\SimilarRequest;
use MetaFox\Page\Http\Requests\v1\Page\StoreRequest;
use MetaFox\Page\Http\Requests\v1\Page\SuggestRequest;
use MetaFox\Page\Http\Requests\v1\Page\UpdateAvatarRequest;
use MetaFox\Page\Http\Requests\v1\Page\UpdateCoverRequest;
use MetaFox\Page\Http\Requests\v1\Page\UpdateRequest;
use MetaFox\Page\Http\Requests\v1\Page\ViewMyPendingRequest;
use MetaFox\Page\Http\Resources\v1\Page\AboutPageForm;
use MetaFox\Page\Http\Resources\v1\Page\CreatePageForm;
use MetaFox\Page\Http\Resources\v1\Page\EditPageForm;
use MetaFox\Page\Http\Resources\v1\Page\InfoPageForm;
use MetaFox\Page\Http\Resources\v1\Page\PageDetail;
use MetaFox\Page\Http\Resources\v1\Page\PageDetail as Detail;
use MetaFox\Page\Http\Resources\v1\Page\PageInfo as InfoDetail;
use MetaFox\Page\Http\Resources\v1\Page\PageItem;
use MetaFox\Page\Http\Resources\v1\Page\PageItemCollection as ItemCollection;
use MetaFox\Page\Http\Resources\v1\Page\PageSimpleCollection;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Policies\PagePolicy;
use MetaFox\Page\Repositories\PageClaimRepositoryInterface;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorRequest;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityCollection;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class PageController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PageController extends ApiController
{
    public function __construct(
        protected PageRepositoryInterface $repository,
        protected UserPrivacyRepositoryInterface $privacyRepository,
        protected PageClaimRepositoryInterface $claimRepository
    ) {
    }

    /**
     * Display a listing of the resource.
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
            if (!policy_check(PagePolicy::class, 'viewOnProfilePage', $context, $owner)) {
                throw new AuthorizationException();
            }

            if (!UserPrivacy::hasAccess($context, $owner, 'page.profile_menu')) {
                return $this->success([]);
            }
        }

        $data = $this->repository->viewPages($context, $owner, $params);

        return $this->success(new ItemCollection($data));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidatorException
     * @throws PermissionDeniedException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();
        app('flood')->checkFloodControlWhenCreateItem(user(), Page::ENTITY_TYPE);

        $page = $this->repository->createPage(user(), $params);

        $message = __p('core::phrase.resource_create_success', [
            'resource_name' => __p('page::phrase.page'),
        ]);

        if (!$page->isApproved()) {
            $message = __p('core::phrase.thanks_for_your_item_for_approval');
        }

        return $this->success(new Detail($page), [], $message);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Detail
     * @throws AuthenticationException|AuthorizationException
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->viewPage(user(), $id);

        return new Detail($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthenticationException|AuthorizationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $page   = $this->repository->updatePage(user(), $id, $params);

        $key     = array_key_first($params);
        $message = __p("page::phrase.page_updated.$key");

        unset($params['location_latitude'], $params['location_longitude']);
        if (count($params) > 1) {
            $message = __p('page::phrase.page_updated.info');
        }

        return $this->success([
            'id' => $page->entityId(),
        ], [], $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deletePage(user(), $id);

        return $this->success([
            'id' => $id,
        ], [], __p('page::phrase.successfully_deleted_the_page'));
    }

    /**
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
        $message   = $isSponsor ? 'core::phrase.resource_sponsored_successfully' : 'core::phrase.resource_unsponsored_successfully';
        $message   = __p($message, ['resource_name' => __p('page::phrase.page')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }

    /**
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

        $message = __p('page::phrase.page_featured_successfully');
        if (!$feature) {
            $message = __p('page::phrase.page_unfeatured_successfully');
        }

        return $this->success([
            'id'          => $id,
            'is_featured' => (int) $feature,
        ], [], $message);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function approve(int $id): JsonResponse
    {
        $page = $this->repository->approve(user(), $id);

        return $this->success(new PageDetail($page), [], __p('page::phrase.approved_successfully'));
    }

    /**
     * @param UpdateAvatarRequest $request
     * @param int                 $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException|ValidationException
     */
    public function updateAvatar(UpdateAvatarRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $image     = $params['image'] ?? null;
        $imageCrop = $params['image_crop'];

        $data         = $this->repository->updateAvatar(user(), $id, $image, $imageCrop);
        $data['user'] = new PageItem($data['user']);

        return $this->success($data, [], __p('page::phrase.successfully_updated_page_avatar'));
    }

    /**
     * @param UpdateCoverRequest $request
     * @param int                $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function updateCover(UpdateCoverRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $data         = $this->repository->updateCover(user(), $id, $params);
        $data['user'] = new PageItem($data['user']);

        return $this->success($data, [], __p('page::phrase.successfully_updated_page_cover'));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function removeCover(int $id): JsonResponse
    {
        $this->repository->removeCover(user(), $id);

        return $this->success([], [], __p('page::phrase.page_cover_photo_removed_successfully'));
    }

    /**
     * @param int|null $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function form(?int $id = null)
    {
        $context = user();
        $page    = new Page();

        if (null !== $id) {
            $page = $this->repository->find($id);
            policy_authorize(PagePolicy::class, 'update', $context, $page);

            return $this->success(new EditPageForm($page));
        }

        policy_authorize(PagePolicy::class, 'create', $context);

        return $this->success(new CreatePageForm($page));
    }

    /**
     * @param int $id
     *
     * @return InfoPageForm
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function infoForm(int $id): InfoPageForm
    {
        $page    = $this->repository->find($id);
        $context = user();

        policy_authorize(PagePolicy::class, 'update', $context, $page);

        return new InfoPageForm($page);
    }

    /**
     * @param int $id
     *
     * @return AboutPageForm
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function aboutForm(int $id): AboutPageForm
    {
        $context = user();

        $page = $this->repository->find($id);

        policy_authorize(PagePolicy::class, 'update', $context, $page);

        return new AboutPageForm($page);
    }

    /**
     * @param ClaimFormRequest $request
     * @param int              $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function claimPage(ClaimFormRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $this->claimRepository->createClaimPage(user(), $id, $params['message']);

        return $this->success([], [], __p('page::phrase.your_claim_request_sent_successfully'));
    }

    /**
     * Display the specified resource info.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function pageInfo(int $id): JsonResponse
    {
        $data = $this->repository->viewPage(user(), $id);

        return $this->success(new InfoDetail($data));
    }

    /**
     * @param SuggestRequest $request
     *
     * @return JsonResource
     * @throws AuthenticationException
     */
    public function suggestion(SuggestRequest $request)
    {
        $params = $request->validated();
        $data   = $this->repository->getSuggestion(user(), $params);

        return new UserEntityCollection($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @param MentionRequest $request
     *
     * @return JsonResource
     * @throws AuthenticationException
     */
    public function getPageForMention(MentionRequest $request)
    {
        $params  = $request->validated();
        $context = user();

        $data = $this->repository->getPageForMention($context, $params);

        return new PageSimpleCollection($data);
    }

    /**
     * @param  int          $id
     * @return JsonResponse
     */
    public function getPrivacySettings(int $id): JsonResponse
    {
        $settings = $this->privacyRepository->getProfileSettings($id);

        return $this->success($settings);
    }

    /**
     * @param  Request                 $request
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function updatePrivacySettings(Request $request, int $id): JsonResponse
    {
        $context = user();
        $page    = $this->repository->find($id);

        policy_authorize(PagePolicy::class, 'update', $context, $page);

        $params = $request->all();
        UserPrivacy::validateProfileSettings($id, $params);
        $this->privacyRepository->updateUserPrivacy($id, $params);

        return $this->success(null, [], __p('core::phrase.updated_successfully'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param SimilarRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function similar(SimilarRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $context = user();

        $data = $this->repository->viewSimilar($context, $params);

        return $this->success(new ItemCollection($data));
    }
}
