<?php

namespace MetaFox\User\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Form\AbstractForm;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\User\Http\Requests\v1\CancelReason\Admin\IndexRequest;
use MetaFox\User\Http\Requests\v1\CancelReason\Admin\StoreRequest;
use MetaFox\User\Http\Requests\v1\CancelReason\Admin\UpdateRequest;
use MetaFox\User\Http\Resources\v1\CancelReason\Admin\CancelReasonDetail as Detail;
use MetaFox\User\Http\Resources\v1\CancelReason\Admin\CancelReasonItem as Item;
use MetaFox\User\Http\Resources\v1\CancelReason\Admin\CancelReasonItemCollection as ItemCollection;
use MetaFox\User\Http\Resources\v1\CancelReason\Admin\CreateForm;
use MetaFox\User\Http\Resources\v1\CancelReason\Admin\EditForm;
use MetaFox\User\Models\CancelReason;
use MetaFox\User\Repositories\CancelReasonRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\User\Http\Controllers\Api\CancelReasonAdminController::$controllers.
 */

/**
 * Class CancelReasonAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @group user
 * @authenticated
 * @admincp
 */
class CancelReasonAdminController extends ApiController
{
    /**
     * @var CancelReasonRepositoryInterface
     */
    public CancelReasonRepositoryInterface $repository;

    /**
     * CancelReasonAdminController constructor.
     *
     * @param CancelReasonRepositoryInterface $repository
     */
    public function __construct(CancelReasonRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return ItemCollection<Item>
     * @group user
     */
    public function index(IndexRequest $request): ItemCollection
    {
        $params = $request->validated();
        $data   = $this->repository->paginate($params['limit'] ?? 100);

        return new ItemCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     * @group user
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
     * @group user
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
     * @group user
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
     * @group user
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * @param int $id
     *
     * @return AbstractForm
     */
    public function editForm(int $id): AbstractForm
    {
        $resource = $this->repository->find($id);

        return new EditForm($resource);
    }

    public function edit($id)
    {
        $resource = $this->repository->find($id);

        return new EditForm($resource);
    }

    public function create()
    {
        $resource = new CancelReason();

        return new CreateForm($resource);
    }

    /**
     * @return AbstractForm
     */
    public function createForm(): AbstractForm
    {
        $resource = new CancelReason();

        return new CreateForm($resource);
    }
}
