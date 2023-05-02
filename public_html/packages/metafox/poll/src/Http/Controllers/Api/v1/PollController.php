<?php

namespace MetaFox\Poll\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorInFeedRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorRequest;
use MetaFox\Poll\Http\Requests\v1\Poll\IndexRequest;
use MetaFox\Poll\Http\Requests\v1\Poll\StoreRequest;
use MetaFox\Poll\Http\Requests\v1\Poll\UpdateRequest;
use MetaFox\Poll\Http\Resources\v1\Poll\IntegrationCreatePollForm;
use MetaFox\Poll\Http\Resources\v1\Poll\PollDetail;
use MetaFox\Poll\Http\Resources\v1\Poll\PollDetail as Detail;
use MetaFox\Poll\Http\Resources\v1\Poll\PollItemCollection as ItemCollection;
use MetaFox\Poll\Http\Resources\v1\Poll\SearchPollForm as SearchForm;
use MetaFox\Poll\Http\Resources\v1\Poll\StatusCreatePollForm;
use MetaFox\Poll\Models\Poll;
use MetaFox\Poll\Policies\PollPolicy;
use MetaFox\Poll\Repositories\PollRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * --------------------------------------------------------------------------
 *  Api Controller
 * --------------------------------------------------------------------------.
 *
 * Assign this class in $controllers of
 * @link \MetaFox\Poll\Http\Controllers\Api\PollController::$controllers;
 */

/**
 * Class PollController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PollController extends ApiController
{
    /**
     * @var PollRepositoryInterface
     */
    public PollRepositoryInterface $repository;

    /**
     * PollController constructor.
     *
     * @param PollRepositoryInterface $repository
     */
    public function __construct(PollRepositoryInterface $repository)
    {
        $this->repository = $repository;
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
        $params = $request->validated();
        $owner  = $context = user();
        if ($params['user_id'] > 0) {
            $owner = UserEntity::getById($params['user_id'])->detail;
            if (policy_check(PollPolicy::class, 'viewOnProfilePage', $context, $owner) == false) {
                throw new AuthorizationException();
            }

            if (UserPrivacy::hasAccess($context, $owner, 'poll.profile_menu') == false) {
                return $this->success([]);
            }
        }

        $data = $this->repository->viewPolls($context, $owner, $params);

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
     * @throws ValidationException
     * @throws PermissionDeniedException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $owner  = $context = user();
        $params = $request->validated();

        app('flood')->checkFloodControlWhenCreateItem(user(), Poll::ENTITY_TYPE);
        app('quota')->checkQuotaControlWhenCreateItem(user(), Poll::ENTITY_TYPE);

        if ($params['owner_id'] > 0) {
            if ($context->entityId() != $params['owner_id']) {
                $owner = UserEntity::getById($params['owner_id'])->detail;
            }
        }

        $poll = $this->repository->createPoll($context, $owner, $params);

        $message = __p('poll::phrase.poll_created_successfully');

        $ownerPendingMessage = $poll->getOwnerPendingMessage();

        if (null !== $ownerPendingMessage) {
            $message = $ownerPendingMessage;
        }

        return $this->success(new Detail($poll), [], $message);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Detail
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->viewPoll(user(), $id);

        return new Detail($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthenticationException | AuthorizationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $data = $this->repository->updatePoll(user(), $id, $request->validated());

        return $this->success(new Detail($data), [], __p('poll::phrase.poll_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deletePoll(user(), $id);

        return $this->success([
            'id' => $id,
        ], [], __p('poll::phrase.poll_deleted_successfully'));
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
        $message   = __p($message, ['resource_name' => __p('poll::phrase.poll')]);

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

        $message = __p('poll::phrase.poll_featured_succesfully');
        if (!$feature) {
            $message = __p('poll::phrase.poll_unfeatured_succesfully');
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
        $poll = $this->repository->approve(user(), $id);

        // @todo recheck response.
        return $this->success(new PollDetail($poll), [], __p('poll::phrase.poll_has_been_approved'));
    }

    /**
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
        $message   = $isSponsor ? 'core::phrase.resource_sponsored_in_feed_successfully' : 'core::phrase.resource_unsponsored_in_feed_successfully';
        $message   = __p($message, ['resource_name' => __p('poll::phrase.poll')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }

    /**
     * @return JsonResponse
     */
    public function searchForm(): JsonResponse
    {
        return $this->success(new SearchForm([]), [], '');
    }

    /**
     * @return JsonResponse
     */
    public function statusForm(): JsonResponse
    {
        return $this->success(new StatusCreatePollForm());
    }

    /**
     * @return JsonResponse
     */
    public function integrationForm(): JsonResponse
    {
        $form = new IntegrationCreatePollForm();

        $form->boot();

        return $this->success($form);
    }
}
