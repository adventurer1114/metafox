<?php

namespace MetaFox\Photo\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Photo\Http\Requests\v1\Category\IndexRequest;
use MetaFox\Photo\Http\Resources\v1\Category\CategoryItemCollection as ItemCollection;
use MetaFox\Photo\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * Class CategoryController.
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
     * @return JsonResource
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function index(IndexRequest $request): JsonResource
    {
        $data = $this->repository->getAllCategories(user(), $request->validated());

        return new ItemCollection($data);
    }
}
