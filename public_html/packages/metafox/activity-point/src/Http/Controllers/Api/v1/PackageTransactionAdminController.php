<?php

namespace MetaFox\ActivityPoint\Http\Controllers\Api\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use MetaFox\ActivityPoint\Http\Requests\v1\PackageTransaction\Admin\IndexRequest;
use MetaFox\ActivityPoint\Http\Resources\v1\PackageTransaction\Admin\PackageTransactionItemCollection as ItemCollection;
use MetaFox\Payment\Models\Order;
use MetaFox\Payment\Repositories\OrderAdminRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\ActivityPoint\Http\Controllers\Api\PackageTransactionAdminController::$controllers;.
 */

/**
 * Class PackageTransactionAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class PackageTransactionAdminController extends ApiController
{
    /**
     * @var OrderAdminRepositoryInterface
     */
    private OrderAdminRepositoryInterface $repository;

    /**
     * PackageTransactionAdminController Constructor.
     *
     * @param OrderAdminRepositoryInterface $repository
     */
    public function __construct(OrderAdminRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse item.
     *
     * @param  IndexRequest            $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $context = user();
        $params  = $request->validated();

        //Not showing status init since no related action can be applied
        Arr::set($params, 'exclude_status', [Order::STATUS_INIT]);

        $data = $this->repository->getTransactions($context, $params);

        return $this->success(new ItemCollection($data));
    }
}
