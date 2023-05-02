<?php

namespace MetaFox\Quiz\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Quiz\Http\Requests\v1\Quiz\IndexRequest;
use MetaFox\Quiz\Http\Requests\v1\Quiz\StoreRequest;
use MetaFox\Quiz\Http\Requests\v1\Quiz\UpdateRequest;
use MetaFox\Quiz\Http\Resources\v1\Quiz\QuizDetail as Detail;
use MetaFox\Quiz\Http\Resources\v1\Quiz\QuizItemCollection as ItemCollection;
use MetaFox\Quiz\Repositories\QuizRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Quiz\Http\Controllers\Api\QuizAdminController::$controllers;
 */

/**
 * Class QuizAdminController.
 */
class QuizAdminController extends ApiController
{
    public QuizRepositoryInterface $repository;

    public function __construct(QuizRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest $request
     * @return JsonResource
     */
    public function index(IndexRequest $request): JsonResource
    {
        $params = $request->validated();
        $data   = $this->repository->get($params);

        return new ItemCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->create($params);

        return new Detail($data);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Detail
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->find($id);

        return new Detail($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest      $request
     * @param  int                $id
     * @return Detail
     * @throws ValidatorException
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->update($params, $id);

        return new Detail($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->success([
            'id' => $id,
        ]);
    }
}
