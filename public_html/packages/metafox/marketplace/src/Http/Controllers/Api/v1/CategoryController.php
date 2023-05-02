<?php

namespace MetaFox\Marketplace\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Marketplace\Http\Requests\v1\Category\DeleteRequest;
use MetaFox\Marketplace\Http\Requests\v1\Category\IndexRequest;
use MetaFox\Marketplace\Http\Requests\v1\Category\StoreRequest;
use MetaFox\Marketplace\Http\Requests\v1\Category\UpdateRequest;
use MetaFox\Marketplace\Http\Resources\v1\Category\CategoryDetail as Detail;
use MetaFox\Marketplace\Http\Resources\v1\Category\CategoryItemCollection;
use MetaFox\Marketplace\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class CategoryController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group marketplace
 */
class CategoryController extends ApiController
{
    /**
     * @var CategoryRepositoryInterface
     */
    public CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browe category.
     *
     * @param  IndexRequest                                   $request
     * @return mixed
     * @throws AuthorizationException|AuthenticationException
     */
    public function index(IndexRequest $request)
    {
        $data = $request->validated();

        $context = user();

        $data = $this->repository->getAllCategories($context, $data);

        return new CategoryItemCollection($data);
    }

    /**
     * Store category.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     * @throws AuthorizationException|AuthenticationException
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->createCategory(user(), $params);

        return new Detail($data);
    }

    /**
     * View category.
     *
     * @param int $id
     *
     * @return Detail
     * @throws AuthorizationException|AuthenticationException
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->viewCategory(user(), $id);

        return new Detail($data);
    }

    /**
     * Update category.
     *
     * @param  UpdateRequest                                  $request
     * @param  int                                            $id
     * @return Detail
     * @throws AuthorizationException|AuthenticationException
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->updateCategory(user(), $id, $params);

        return new Detail($data);
    }

    /**
     * Delete category.
     *
     * @param DeleteRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function destroy(DeleteRequest $request, int $id): JsonResponse
    {
        $params    = $request->validated();
        $iCategory = (int) $params['category'];

        $this->repository->deleteCategory(user(), $id, $iCategory);

        return $this->success([], [], __p('marketplace::phrase.successfully_deleted_the_category'));
    }
}
