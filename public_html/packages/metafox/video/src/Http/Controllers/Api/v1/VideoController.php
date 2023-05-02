<?php

namespace MetaFox\Video\Http\Controllers\Api\v1;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorInFeedRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorRequest;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\Video\Http\Requests\v1\Video\IndexRequest;
use MetaFox\Video\Http\Requests\v1\Video\StoreRequest;
use MetaFox\Video\Http\Requests\v1\Video\UpdateRequest;
use MetaFox\Video\Http\Resources\v1\Video\VideoDetail;
use MetaFox\Video\Http\Resources\v1\Video\VideoItemCollection;
use MetaFox\Video\Policies\VideoPolicy;
use MetaFox\Video\Repositories\VideoRepositoryInterface;

/**
 * Class CategoryController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @group video
 * @authenticated
 */
class VideoController extends ApiController
{
    public VideoRepositoryInterface $repository;

    public function __construct(VideoRepositoryInterface $repository)
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
        $params  = $request->validated();
        $context = user();
        $owner   = $context;
        if ($params['user_id'] > 0) {
            $owner = UserEntity::getById($params['user_id'])->detail;

            if (policy_check(VideoPolicy::class, 'viewOnProfilePage', $context, $owner) == false) {
                throw new AuthorizationException();
            }

            if (UserPrivacy::hasAccess($context, $owner, 'video.profile_menu') == false) {
                return $this->success([]);
            }
        }
        $data = $this->repository->viewVideos($context, $owner, $params);

        return $this->success(new VideoItemCollection($data));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function show(int $id): JsonResponse
    {
        $context = user();
        $video   = $this->repository->viewVideo($context, $id);

        if (null == $video) {
            return $this->error(
                __p('core::phrase.the_entity_name_you_are_looking_for_can_not_be_found', ['entity_name' => 'video']),
                403
            );
        }

        return $this->success(new VideoDetail($video), [], '');
    }

    /**
     * Create a resource.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws Exception
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $context = $owner = user();
        $params  = $request->validated();

        if ($params['owner_id'] > 0) {
            if ($context->entityId() != $params['owner_id']) {
                $owner = UserEntity::getById($params['owner_id'])->detail;
            }
        }

        $video = $this->repository->createVideo($context, $owner, $params);

        $pendingMessage = $video->getOwnerPendingMessage();

        if ($video->in_process) {
            return $this->info(new VideoDetail($video), [], __p('video::phrase.video_in_process_message'));
        }

        $message = $pendingMessage ?? __p('video::phrase.video_was_uploaded_successfully');

        return $this->success(new VideoDetail($video), [], $message);
    }

    /**
     * Update a resource.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $context = user();
        $params  = $request->validated();
        $data    = $this->repository->updateVideo($context, $id, $params);

        return $this->success(new VideoDetail($data), [], __p('video::phrase.video_updated_successfully'));
    }

    /**
     * Delete a resource.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $context  = user();
        $resource = $this->repository->viewVideo($context, $id);

        $collection       = $resource->group;
        $collectionFeed   = $collection instanceof Content ? $collection->activity_feed : null;
        $collectionFeedId = 0;

        if ($collectionFeed instanceof Content) {
            $collectionFeedId = $collectionFeed->entityId();
        }

        $this->repository->deleteVideo($context, $id);

        return $this->success([
            'id'      => $id,
            'feed_id' => $collectionFeedId,
        ], [], __p('video::phrase.video_deleted_successfully'));
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

        $message = $isSponsor ? 'core::phrase.resource_sponsored_successfully' : 'core::phrase.resource_unsponsored_successfully';
        $message = __p($message, ['resource_name' => __p('video::phrase.video')]);

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

        $message = __p('video::phrase.video_featured_successfully');
        if (!$feature) {
            $message = __p('video::phrase.video_unfeatured_successfully');
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
        $video = $this->repository->approve(user(), $id);

        // @todo recheck response.
        return $this->success(new VideoDetail($video), [], __p('video::phrase.video_has_been_approved'));
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

        $message = $isSponsor ? 'core::phrase.resource_sponsored_successfully' : 'core::phrase.resource_unsponsored_successfully';
        $message = __p($message, ['resource_name' => __p('video::phrase.video')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }

    public function callback(string $provider, Request $request): JsonResponse
    {
        $response = app('events')->dispatch('video.callback', [$request, $provider], true);

        if (!$response) {
            $this->error('Something went wrong');
        }

        return $this->success([
            'success' => $response,
        ], [], '');
    }
}
