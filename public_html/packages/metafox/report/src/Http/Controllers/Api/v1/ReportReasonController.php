<?php

namespace MetaFox\Report\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Report\Http\Requests\v1\ReportReason\IndexRequest;
use MetaFox\Report\Http\Requests\v1\ReportReason\StoreRequest;
use MetaFox\Report\Http\Requests\v1\ReportReason\UpdateRequest;
use MetaFox\Report\Http\Resources\v1\ReportReason\ReportReasonDetail as Detail;
use MetaFox\Report\Http\Resources\v1\ReportReason\ReportReasonItemCollection as ItemCollection;
use MetaFox\Report\Repositories\ReportReasonRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class ReportReasonController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group report
 * @admincp
 */
class ReportReasonController extends ApiController
{
    /**
     * @var ReportReasonRepositoryInterface
     */
    private ReportReasonRepositoryInterface $repository;

    /**
     * @param ReportReasonRepositoryInterface $repository
     */
    public function __construct(ReportReasonRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse report reasons.
     *
     * @param IndexRequest $request
     *
     * @return JsonResource
     * @throws AuthorizationException|AuthenticationException
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();
        $data   = $this->repository->viewReasons(user(), $params);

        return new ItemCollection($data);
    }

    /**
     * Store a new report reason.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     * @throws AuthorizationException|AuthenticationException
     * @hideFromAPIDocumentation
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->createReason(user(), $params);

        return new Detail($data);
    }

    /**
     * Display a report reason.
     *
     * @param int $id
     *
     * @return Detail
     * @throws AuthorizationException|AuthenticationException
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->viewReason(user(), $id);

        return new Detail($data);
    }

    /**
     * Update a report reason.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return Detail
     * @throws AuthorizationException|AuthenticationException
     * @hideFromAPIDocumentation
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->updateReason(user(), $id, $params);

        return new Detail($data);
    }

    /**
     * Remove a report reason.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     * @hideFromAPIDocumentation
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deleteReason(user(), $id);

        return $this->success([
            'id' => $id,
        ]);
    }
}
