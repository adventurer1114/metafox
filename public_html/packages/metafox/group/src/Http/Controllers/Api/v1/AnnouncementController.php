<?php

namespace MetaFox\Group\Http\Controllers\Api\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Group\Http\Requests\v1\Announcement\DeleteRequest;
use MetaFox\Group\Http\Requests\v1\Announcement\HiddenRequest;
use MetaFox\Group\Http\Requests\v1\Announcement\IndexRequest;
use MetaFox\Group\Http\Requests\v1\Announcement\StoreRequest;
use MetaFox\Group\Http\Resources\v1\Announcement\AnnouncementItemCollection as ItemCollection;
use MetaFox\Group\Repositories\AnnouncementRepositoryInterface;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Group\Http\Controllers\Api\AnnouncementController::$controllers;.
 */

/**
 * Class AnnouncementController.
 * @codeCoverageIgnore
 * @ignore
 */
class AnnouncementController extends ApiController
{
    /**
     * AnnouncementController Constructor.
     *
     * @param AnnouncementRepositoryInterface $repository
     * @param GroupRepositoryInterface        $groupRepository
     */
    public function __construct(
        protected AnnouncementRepositoryInterface $repository,
        protected GroupRepositoryInterface $groupRepository
    ) {
    }

    /**
     * Browse item.
     *
     * @param  IndexRequest                $request
     * @return ItemCollection|JsonResponse
     * @throws AuthenticationException
     */
    public function index(IndexRequest $request)
    {
        $params  = $request->validated();
        $groupId = $params['group_id'];
        $group   = $this->groupRepository->find($groupId);

        if (!$group->isMember(user())) {
            return $this->success();
        }

        $data = $this->repository->viewAnnouncements(user(), $params);

        return new ItemCollection($data);
    }

    /**
     * Store item.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params   = $request->validated();
        $announce = $this->repository->createAnnouncement(user(), $params);

        $response = ResourceGate::asEmbed($announce->item);

        return $this->success($response, [], __p('group::phrase.mark_as_announcement_successfully'));
    }

    /**
     * Delete item.
     *
     * @param DeleteRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function removeAnnouncement(DeleteRequest $request): JsonResponse
    {
        $params   = $request->validated();
        $item     = $this->repository->deleteAnnouncement(user(), $params);
        $response = ResourceGate::asEmbed($item);

        return $this->success($response, [], __p('group::phrase.remove_announcement_successfully'));
    }

    /**
     * @throws AuthenticationException
     */
    public function hide(HiddenRequest $request): ItemCollection|JsonResponse
    {
        $params = $request->validated();
        $this->repository->hideAnnouncement(user(), $params);

        $data = $this->repository->viewAnnouncements(user(), $params);

        if (empty($data['data'])) {
            return $this->success();
        }

        return new ItemCollection($data);
    }
}
