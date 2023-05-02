<?php

namespace MetaFox\Page\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Page\Http\Requests\v1\PageCategory\IndexRequest;
use MetaFox\Page\Http\Resources\v1\PageCategory\PageCategoryItemCollection as ItemCollection;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * Class PageCategoryController.
 */
class PageCategoryController extends ApiController
{
    public PageCategoryRepositoryInterface $repository;

    public function __construct(PageCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return JsonResource
     * @throws AuthorizationException|AuthenticationException
     */
    public function index(IndexRequest $request)
    {
        $data = $this->repository->getAllCategories(user(), $request->validated());

        return new ItemCollection($data);
    }
}
