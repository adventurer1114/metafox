<?php

namespace MetaFox\Marketplace\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\Marketplace\Http\Requests\v1\Category\IndexRequest;
use MetaFox\Marketplace\Http\Resources\v1\Category\CategoryDetail as Detail;
use MetaFox\Marketplace\Http\Resources\v1\Category\CategoryItemCollection;
use MetaFox\Marketplace\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

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
     * @param  IndexRequest            $request
     * @return CategoryItemCollection
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request)
    {
        $data = $request->validated();

        $context = user();

        $data = $this->repository->getAllCategories($context, $data);

        return new CategoryItemCollection($data);
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
}
