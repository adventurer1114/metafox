<?php

namespace MetaFox\Music\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Music\Http\Requests\v1\Genre\IndexRequest;
use MetaFox\Music\Http\Resources\v1\Genre\GenreItemCollection;
use MetaFox\Music\Repositories\GenreRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * Class GenreController.
 * @ignore
 * @codeCoverageIgnore
 * @group blog
 * @authenticated
 */
class GenreController extends ApiController
{
    /**
     * @var GenreRepositoryInterface
     */
    private GenreRepositoryInterface $repository;

    /**
     * @param GenreRepositoryInterface $repository
     */
    public function __construct(GenreRepositoryInterface $repository)
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

        return $this->success(new GenreItemCollection($data));
    }
}
