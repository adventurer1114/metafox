<?php

namespace MetaFox\Video\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Video\Http\Requests\v1\Category\DeleteRequest;
use MetaFox\Video\Http\Requests\v1\Category\IndexRequest;
use MetaFox\Video\Http\Requests\v1\Category\StoreRequest;
use MetaFox\Video\Http\Requests\v1\Category\UpdateRequest;
use MetaFox\Video\Http\Resources\v1\Category\Admin\StoreCategoryForm;
use MetaFox\Video\Http\Resources\v1\Category\CategoryDetail;
use MetaFox\Video\Http\Resources\v1\Category\CategoryItemCollection;
use MetaFox\Video\Repositories\CategoryRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class CategoryController
 * @ignore
 * @codeCoverageIgnore
 * @group video
 * @authenticated
 */
class CategoryController extends ApiController
{
    public CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $data = $this->repository->getAllCategories(user(), $request->validated());
        return $this->success(new CategoryItemCollection($data));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $category = $this->repository->createCategory(user(), $request->validated());

        return $this->success(new CategoryDetail($category), [
            'nextAction' => [
                'type'    => 'navigate',
                'payload' => [
                    'url' => '/admincp/video/category/browse',
                ],
            ],
        ], __p('core::phrase.resource_create_success', [
            'resource_name' => __p('core::phrase.category'),
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $category = $this->repository->updateCategory(user(), $id, $request->validated());

        return $this->success(new CategoryDetail($category), [], __p('core::phrase.updated_successfully'));
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
        $category = $this->repository->viewCategory(user(), $id);

        return $this->success(new CategoryDetail($category));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function destroy(DeleteRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $newCategoryId = $params['new_category_id'];
        $this->repository->deleteCategory(user(), $id, $newCategoryId);

        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * View creation form.
     *
     * @return StoreCategoryForm
     */
    public function create(): StoreCategoryForm
    {
        return new StoreCategoryForm();
    }
}
