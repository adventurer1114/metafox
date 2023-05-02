<?php

namespace MetaFox\Blog\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Blog\Http\Requests\v1\Blog\IndexRequest;
use MetaFox\Blog\Http\Requests\v1\Blog\StoreRequest;
use MetaFox\Blog\Http\Requests\v1\Blog\UpdateRequest;
use MetaFox\Blog\Http\Resources\v1\Blog\BlogDetail as Detail;
use MetaFox\Blog\Http\Resources\v1\Blog\BlogItemCollection as ItemCollection;
use MetaFox\Blog\Repositories\BlogRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 | stub: /packages/controllers/admin_api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Blog\Http\Controllers\Api\BlogController::$controllers;
 */

/**
 * Class BlogAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group blog
 */
class BlogAdminController extends ApiController
{
    /**
     * @var BlogRepositoryInterface
     */
    private BlogRepositoryInterface $repository;

    /**
     * @param BlogRepositoryInterface $repository
     */
    public function __construct(BlogRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse blogs.
     *
     * @param  IndexRequest $request
     * @return mixed
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();
        $data   = $this->repository->get($params);

        return new ItemCollection($data);
    }

    /**
     * Create blog.
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
     * View blog.
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
     * Update blog.
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
     * Remove blog.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->success([
            'id' => $id,
        ]);
    }
}
