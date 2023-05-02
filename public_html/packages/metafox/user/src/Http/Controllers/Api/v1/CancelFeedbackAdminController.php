<?php

namespace MetaFox\User\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\User\Http\Requests\v1\CancelFeedback\Admin\IndexRequest;
use MetaFox\User\Http\Requests\v1\CancelFeedback\Admin\StoreRequest;
use MetaFox\User\Http\Requests\v1\CancelFeedback\Admin\UpdateRequest;
use MetaFox\User\Http\Resources\v1\CancelFeedback\Admin\CancelFeedbackDetail as Detail;
use MetaFox\User\Http\Resources\v1\CancelFeedback\Admin\CancelFeedbackItemCollection as ItemCollection;
use MetaFox\User\Http\Resources\v1\User\UserItem;
use MetaFox\User\Repositories\CancelFeedbackRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\User\Http\Controllers\Api\CancelFeedbackAdminController::$controllers.
 */

/**
 * Class CancelFeedbackAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @group user
 * @authenticated
 * @admincp
 */
class CancelFeedbackAdminController extends ApiController
{
    /**
     * @var CancelFeedbackRepositoryInterface
     */
    public $repository;

    /**
     * CancelFeedbackAdminController constructor.
     *
     * @param CancelFeedbackRepositoryInterface $repository
     */
    public function __construct(CancelFeedbackRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return ItemCollection<UserItem>
     * @group admin/user/feedback
     */
    public function index(IndexRequest $request): ItemCollection
    {
        $params = $request->validated();
        $data   = $this->repository->get($params);

        return new ItemCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     * @group admin/user/feedback
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->create($params);

        return new Detail($data);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Detail
     * @group admin/user/feedback
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->find($id);

        return new Detail($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return Detail
     * @throws ValidatorException
     * @group admin/user/feedback
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->update($params, $id);

        return new Detail($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @group admin/user/feedback
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->success([
            'id' => $id,
        ]);
    }
}
