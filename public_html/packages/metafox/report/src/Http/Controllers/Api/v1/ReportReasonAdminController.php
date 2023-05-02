<?php

namespace MetaFox\Report\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\OrderingRequest;
use MetaFox\Report\Http\Requests\v1\ReportReason\Admin\StoreRequest;
use MetaFox\Report\Http\Requests\v1\ReportReason\Admin\UpdateRequest;
use MetaFox\Report\Http\Resources\v1\ReportReason\Admin\CreateReportReasonForm;
use MetaFox\Report\Http\Resources\v1\ReportReason\Admin\EditReportReasonForm;
use MetaFox\Report\Http\Resources\v1\ReportReason\Admin\ReportReasonDetail as Detail;
use MetaFox\Report\Http\Resources\v1\ReportReason\Admin\ReportReasonItemCollection as ItemCollection;
use MetaFox\Report\Repositories\ReportReasonAdminRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Report\Http\Controllers\Api\ReportReasonAdminController::$controllers.
 */

/**
 * Class ReportReasonAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group report
 * @admincp
 */
class ReportReasonAdminController extends ApiController
{
    /**
     * @var ReportReasonAdminRepositoryInterface
     */
    private ReportReasonAdminRepositoryInterface $repository;

    /**
     * @param ReportReasonAdminRepositoryInterface $repository
     */
    public function __construct(ReportReasonAdminRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display report reasons.
     *
     * @return JsonResource
     * @throws AuthorizationException|AuthenticationException
     */
    public function index(): JsonResource
    {
        $context = user();
        $data    = $this->repository->viewReasons($context);

        return new ItemCollection($data);
    }

    /**
     * Store a report reason.
     *
     * @param StoreRequest $request
     *
     * @return JsonResource
     * @throws ValidatorException
     */
    public function store(StoreRequest $request): JsonResource
    {
        $params = $request->validated();
        $data   = $this->repository->create($params);

        return new Detail($data);
    }

    /**
     * Update a report reason.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResource
     * @throws ValidatorException
     */
    public function update(UpdateRequest $request, int $id): JsonResource
    {
        $params = $request->validated();
        $data   = $this->repository->update($params, $id);

        return new Detail($data);
    }

    /**
     * Remove a report reason.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * Get updating form.
     *
     * @param int $id
     *
     * @return JsonResource
     */
    public function create(int $id): JsonResource
    {
        $resource = $this->repository->find($id);

        return new EditReportReasonForm($resource);
    }

    /**
     * Get creation form.
     *
     * @return JsonResource
     */
    public function edit(): JsonResource
    {
        return new CreateReportReasonForm();
    }

    public function order(OrderingRequest $request): JsonResponse
    {
        $context = user();
        $params  = $request->validated();

        $this->repository->orderReasons($context, $params);

        return $this->success([], [], __p('report::phrase.reason_reorder_successfully'));
    }
}
