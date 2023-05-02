<?php

namespace MetaFox\Video\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Video\Http\Requests\v1\Video\IndexRequest;
use MetaFox\Video\Http\Requests\v1\Video\StoreRequest;
use MetaFox\Video\Http\Requests\v1\Video\UpdateRequest;
use MetaFox\Video\Http\Resources\v1\Category\Admin\DestroyCategoryForm;
use MetaFox\Video\Http\Resources\v1\Category\Admin\StoreCategoryForm;
use MetaFox\Video\Http\Resources\v1\Category\Admin\UpdateCategoryForm;
use MetaFox\Video\Http\Resources\v1\Video\VideoDetail as Detail;
use MetaFox\Video\Http\Resources\v1\Video\VideoItemCollection as ItemCollection;
use MetaFox\Video\Repositories\VideoRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Video\Http\Controllers\Api\VideoAdminController::$controllers;
 */

/**
 * Class VideoAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @group video
 * @authenticated
 * @admincp
 */
class VideoAdminController extends ApiController
{
    public VideoRepositoryInterface $repository;

    public function __construct(VideoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest $request
     * @return mixed
     */
    public function index(IndexRequest $request)
    {
        $params = $request->all();
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
    public function store(StoreRequest $request)
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
    public function show($id)
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
    public function update(UpdateRequest $request, $id)
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
    public function destroy($id)
    {
        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * View creation form.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $form = new StoreCategoryForm();

        return $this->success($form);
    }

    /**
     * View creation form.
     *
     * @param  int          $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $item = $this->repository->find($id);

        $form = new UpdateCategoryForm($item);

        return $this->success($form);
    }

    /**
     * View creation form.
     *
     * @param  int          $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $form = new DestroyCategoryForm();

        app()->call([$form, 'boot'], ['id' => $id]);

        return $this->success($form);
    }
}
