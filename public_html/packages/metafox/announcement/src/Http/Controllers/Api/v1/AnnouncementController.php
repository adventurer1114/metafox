<?php

namespace MetaFox\Announcement\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use MetaFox\Announcement\Http\Requests\v1\Announcement\HideRequest;
use MetaFox\Announcement\Http\Requests\v1\Announcement\IndexRequest;
use MetaFox\Announcement\Http\Resources\v1\Announcement\AnnouncementDetail as Detail;
use MetaFox\Announcement\Http\Resources\v1\Announcement\AnnouncementItemCollection as ItemCollection;
use MetaFox\Announcement\Repositories\AnnouncementRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * --------------------------------------------------------------------------
 *  Api Controller
 * --------------------------------------------------------------------------.
 *
 * Assign this class in $controllers of
 * @link \MetaFox\Announcement\Http\Controllers\Api\AnnouncementController::$controllers;
 */

/**
 * Class AnnouncementController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group announcement
 */
class AnnouncementController extends ApiController
{
    /**
     * @var AnnouncementRepositoryInterface
     */
    private AnnouncementRepositoryInterface $repository;

    /**
     * @param AnnouncementRepositoryInterface $repository
     */
    public function __construct(AnnouncementRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse announcement.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     * @group announcement
     * @throws AuthorizationException|AuthenticationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();

        $data = $this->repository->viewAnnouncements(user(), $params);

        $resources = new ItemCollection($data);

        $responseData = $resources->toResponse($request)->getData(true);

        $count = $this->repository->getTotalUnread(user());

        $meta = Arr::get($responseData, 'meta', []);

        Arr::set($meta, 'total_unread', $count);

        return $this->success($resources, $meta);
    }

    /**
     * View announcement.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group announcement
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->repository->viewAnnouncement(user(), $id);

        return $this->success(new Detail($data));
    }

    /**
     * Hide announcement.
     *
     * @param HideRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group announcement
     */
    public function hide(HideRequest $request): JsonResponse
    {
        $params = $request->validated();
        $this->repository->hideAnnouncement(user(), $params['announcement_id']);

        return $this->success([
            'id' => $params['announcement_id'],
        ]);
    }

    /**
     * @throws AuthenticationException
     */
    public function close(): JsonResponse
    {
        $this->repository->closeAnnouncement(user());

        return $this->success();
    }
}
