<?php

namespace MetaFox\Notification\Http\Controllers\Api\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Notification\Http\Requests\v1\Type\Admin\UpdateRequest;
use MetaFox\Notification\Http\Resources\v1\Type\Admin\TypeDetail as Detail;
use MetaFox\Notification\Http\Resources\v1\Type\Admin\TypeItemCollection as ItemCollection;
use MetaFox\Notification\Http\Resources\v1\Type\Admin\UpdateTypeForm;
use MetaFox\Notification\Repositories\TypeRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use Prettus\Validator\Exceptions\ValidatorException;
use Throwable;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Notification\Http\Controllers\Api\TypeAdminController::$controllers.
 */

/**
 * Class TypeAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group admincp/notification
 */
class TypeAdminController extends ApiController
{
    /**
     * @var TypeRepositoryInterface
     */
    public TypeRepositoryInterface $repository;

    public function __construct(TypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse all type.
     *
     * @return mixed
     */
    public function index()
    {
        $data = $this->repository->viewTypes();

        return new ItemCollection($data);
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

    /**
     * Update type.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return Detail
     * @throws ValidatorException
     * @throws AuthenticationException
     * @throws Throwable
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->updateType(user(), $id, $params);

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

    /**
     * View edit form.
     *
     * @param int $id
     *
     * @return JsonResource
     */
    public function edit(int $id): JsonResource
    {
        $resource = $this->repository->find($id);

        return new UpdateTypeForm($resource);
    }
}
