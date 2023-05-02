<?php

namespace MetaFox\Photo\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Photo\Http\Resources\v1\PhotoGroupItem\PhotoGroupItemItemCollection;
use MetaFox\Photo\Repositories\PhotoGroupRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Photo\Http\Controllers\Api\PhotoGroupController::$controllers;
 */

/**
 * Class PhotoGroupController.
 */
class PhotoGroupController extends ApiController
{
    /**
     * @var PhotoGroupRepositoryInterface
     */
    public $repository;

    public function __construct(PhotoGroupRepositoryInterface $repository)
    {
        $this->repository = $repository;
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
        $data = $this->repository->viewPhotoGroup(user(), $id);

        return $this->success(new PhotoGroupItemItemCollection($data->items));
    }
}
