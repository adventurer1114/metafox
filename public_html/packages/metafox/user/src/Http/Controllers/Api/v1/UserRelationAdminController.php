<?php

namespace MetaFox\User\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Form\AbstractForm;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\User\Http\Requests\v1\UserRelation\Admin\IndexRequest;
use MetaFox\User\Http\Requests\v1\UserRelation\Admin\StoreRequest;
use MetaFox\User\Http\Requests\v1\UserRelation\Admin\UpdateRequest;
use MetaFox\User\Http\Resources\v1\UserRelation\Admin\CreateForm;
use MetaFox\User\Http\Resources\v1\UserRelation\Admin\EditForm;
use MetaFox\User\Http\Resources\v1\UserRelation\Admin\UserRelationDetail as Detail;
use MetaFox\User\Http\Resources\v1\UserRelation\Admin\UserRelationItem as Item;
use MetaFox\User\Http\Resources\v1\UserRelation\Admin\UserRelationItemCollection as ItemCollection;
use MetaFox\User\Models\UserRelation;
use MetaFox\User\Repositories\UserRelationRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\User\Http\Controllers\Api\UserRelationAdminController::$controllers.
 */

/**
 * Class UserRelationAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @group user
 * @authenticated
 * @admincp
 */
class UserRelationAdminController extends ApiController
{
    /**
     * @var UserRelationRepositoryInterface
     */
    public UserRelationRepositoryInterface $repository;

    /**
     * UserRelationAdminController constructor.
     *
     * @param UserRelationRepositoryInterface $repository
     */
    public function __construct(UserRelationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse relation.
     *
     * @param IndexRequest $request
     *
     * @return ItemCollection<Item>
     * @group admin/user/relation
     * @authenticated
     */
    public function index(IndexRequest $request): ItemCollection
    {
        $params = $request->validated();
        $data   = $this->repository->paginate($params['limit'] ?? 100);

        return new ItemCollection($data);
    }

    /**
     * Create user relation.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     * @group admin/user/relation
     * @authenticated
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->create($params);

        return new Detail($data);
    }

    /**
     * View user relationship.
     *
     * @param int $id
     *
     * @return Detail
     * @group admin/user/relation
     * @authenticated
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->find($id);

        return new Detail($data);
    }

    /**
     * Update user relationship.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return Detail
     * @throws ValidatorException
     * @group admin/user/relation
     * @authenticated
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->update($params, $id);

        return new Detail($data);
    }

    /**
     * Delete user relationship`.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @group admin/user/relation
     * @authenticated
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * View editing form.
     *
     * @param int $id
     *
     * @return AbstractForm
     * @group admin/user/relation
     * @authenticated
     */
    public function editForm(int $id): AbstractForm
    {
        return new EditForm($this->repository->find($id));
    }

    /**
     * View creation form.
     * @return AbstractForm
     * @group admin/user/relation
     * @authenticated
     */
    public function createForm(): AbstractForm
    {
        return new CreateForm(new UserRelation());
    }
}
