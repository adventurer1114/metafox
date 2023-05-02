<?php

namespace MetaFox\ActivityPoint\Http\Controllers\Api\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\ActivityPoint\Http\Requests\v1\PackageTransaction\IndexRequest;
use MetaFox\ActivityPoint\Http\Resources\v1\PackageTransaction\PackageTransactionItemCollection as ItemCollection;
use MetaFox\Payment\Repositories\OrderRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\ActivityPoint\Http\Controllers\Api\PackageTransactionController::$controllers;
 */

/**
 * Class PackageTransactionController.
 * @codeCoverageIgnore
 * @ignore
 * @authenticated
 * @group activitypoint
 */
class PackageTransactionController extends ApiController
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $repository;

    /**
     * PackageTransactionController Constructor.
     *
     * @param OrderRepositoryInterface $repository
     */
    public function __construct(OrderRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse item.
     *
     * @param IndexRequest $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $context = user();
        $params = $request->validated();

        $data = $this->repository->getTransactions($context, $params);

        return $this->success(new ItemCollection($data));
    }
}
