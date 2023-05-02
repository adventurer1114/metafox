<?php

namespace MetaFox\Activity\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Activity\Http\Requests\v1\Snooze\IndexRequest;
use MetaFox\Activity\Http\Resources\v1\Snooze\SnoozeItemCollection as ItemCollection;
use MetaFox\Activity\Repositories\SnoozeRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\User\Support\Facades\UserEntity;

/**
 * --------------------------------------------------------------------------
 *  Api Controller
 * --------------------------------------------------------------------------.
 *
 * Assign this class in $controllers of
 * @link \MetaFox\Activity\Http\Controllers\Api\SnoozeController::$controllers;
 */

/**
 * Class SnoozeController.
 * @ignore
 * @codeCoverageIgnore
 * @group feed
 * @authenticated
 */
class SnoozeController extends ApiController
{
    /**
     * @var SnoozeRepositoryInterface
     */
    public SnoozeRepositoryInterface $repository;

    /**
     * @param SnoozeRepositoryInterface $repository
     */
    public function __construct(SnoozeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse snooze.
     *
     * @param  IndexRequest            $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $user   = user();
        $params = $request->validated();

        if (isset($params['user_id'])) {
            $user = UserEntity::getById($params['user_id'])->detail;
        }

        $limit = !empty($params['limit']) ? $params['limit'] : Pagination::DEFAULT_ITEM_PER_PAGE;
        $type  = !empty($params['type']) ? $params['type'] : null;
        $q     = !empty($params['q']) ? $params['q'] : null;

        $resource = $this->repository->getSnoozes($user, $type, $q, $limit);

        return $this->success(new ItemCollection($resource), [], '');
    }

    /**
     * Remove snooze.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException|AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $context = user();

        $this->repository->deleteSnooze($context, $id);

        return $this->success([]);
    }
}
