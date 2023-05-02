<?php

namespace MetaFox\Event\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Event\Http\Requests\v1\Category\IndexRequest;
use MetaFox\Event\Http\Resources\v1\Category\CategoryItemCollection as ItemCollection;
use MetaFox\Event\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * Class CategoryController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group event
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
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $data = $this->repository->getAllCategories(user(), $request->validated());
        return $this->success(new ItemCollection($data));
    }

}
