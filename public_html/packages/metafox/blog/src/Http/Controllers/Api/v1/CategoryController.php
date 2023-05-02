<?php

namespace MetaFox\Blog\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Blog\Http\Requests\v1\Category\IndexRequest;
use MetaFox\Blog\Http\Resources\v1\Category\CategoryItemCollection;
use MetaFox\Blog\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * Class CategoryController.
 * @ignore
 * @codeCoverageIgnore
 * @group blog
 * @authenticated
 */
class CategoryController extends ApiController
{
    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $repository;

    /**
     * @param CategoryRepositoryInterface $repository
     */
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
    public function index(IndexRequest $request)
    {
        $data = $this->repository->getAllCategories(user(), $request->validated());
        return $this->success(new CategoryItemCollection($data));
    }
}
