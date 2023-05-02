<?php

namespace MetaFox\Activity\Http\Controllers\Api\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Activity\Http\Requests\v1\Type\Admin\StoreRequest;
use MetaFox\Activity\Http\Requests\v1\Type\Admin\UpdateRequest;
use MetaFox\Activity\Http\Resources\v1\Type\Admin\TypeDetail as Detail;
use MetaFox\Activity\Http\Resources\v1\Type\Admin\TypeItem;
use MetaFox\Activity\Http\Resources\v1\Type\Admin\TypeItemCollection as ItemCollection;
use MetaFox\Activity\Http\Resources\v1\Type\Admin\UpdateTypeForm;
use MetaFox\Activity\Repositories\TypeRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Activity\Http\Controllers\Api\TypeAdminController::$controllers.
 */

/**
 * Class TypeAdminController.
 * @group admin/feed
 * @authenticated
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 */
class TypeAdminController extends ApiController
{
    /**
     * @var TypeRepositoryInterface
     */
    public TypeRepositoryInterface $repository;

    /**
     * @param TypeRepositoryInterface $repository
     */
    public function __construct(TypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse Type.
     *
     * @return ItemCollection<TypeItem>
     */
    public function index(): ItemCollection
    {
        $data = $this->repository->all();

        return new ItemCollection($data);
    }

    /**
     * Create type.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->create($params);

        return new Detail($data);
    }

    /**
     * View type.
     *
     * @param int $id
     *
     * @return Detail
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->find($id);

        return new Detail($data);
    }

    public function edit($id): JsonResponse
    {
        $item = $this->repository->find($id);

        $form = new UpdateTypeForm($item);

        return $this->success($form);
    }

    /**
     * Update type.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return Detail
     * @throws ValidatorException
     * @throws AuthenticationException
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();

        $data = $this->repository->updateType(user(), $id, $params);

        return new Detail($data);
    }

    /**
     * Delete type.
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
}
