<?php

namespace MetaFox\Authorization\Http\Controllers\Api\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Authorization\Http\Resources\v1\Device\DeviceDetail as Detail;
use MetaFox\Authorization\Repositories\DeviceRepositoryInterface;
use MetaFox\Authorization\Http\Requests\v1\Device\StoreRequest;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Authorization\Http\Controllers\Api\DeviceController::$controllers;
 */

/**
 * Class DeviceController.
 * @codeCoverageIgnore
 * @ignore
 */
class DeviceController extends ApiController
{
    /**
     * @var DeviceRepositoryInterface
     */
    private DeviceRepositoryInterface $repository;

    /**
     * DeviceController Constructor.
     *
     * @param DeviceRepositoryInterface $repository
     */
    public function __construct(DeviceRepositoryInterface $repository)
    {
        $this->repository = $repository;
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
        $context  = user();
        $device   = $this->repository->updateOrCreateDevice($context, $params);

        app('firebase.fcm')->addUserDeviceGroup($context->entityId(), [$device->device_token]);

        return $this->success(new Detail($device));
    }
}
