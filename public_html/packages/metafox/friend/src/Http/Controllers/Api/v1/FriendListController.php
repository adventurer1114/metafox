<?php

namespace MetaFox\Friend\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Friend\Http\Requests\v1\FriendList\AssignFriendListRequest;
use MetaFox\Friend\Http\Requests\v1\FriendList\IndexRequest;
use MetaFox\Friend\Http\Requests\v1\FriendList\ManageFriendListRequest;
use MetaFox\Friend\Http\Requests\v1\FriendList\StoreRequest;
use MetaFox\Friend\Http\Requests\v1\FriendList\UpdateRequest;
use MetaFox\Friend\Http\Resources\v1\FriendList\CreateFriendListForm;
use MetaFox\Friend\Http\Resources\v1\FriendList\EditFriendListForm;
use MetaFox\Friend\Http\Resources\v1\FriendList\FriendListDetail as Detail;
use MetaFox\Friend\Http\Resources\v1\FriendList\FriendListItemCollection as ItemCollection;
use MetaFox\Friend\Policies\FriendListPolicy;
use MetaFox\Friend\Repositories\FriendListRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class FriendListController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 * @group friend
 * @authenticated
 */
class FriendListController extends ApiController
{
    /**
     * @var FriendListRepositoryInterface
     */
    private FriendListRepositoryInterface $repository;

    /**
     * @param FriendListRepositoryInterface $repository
     */
    public function __construct(FriendListRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse friend lists.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $context = user();

        if (!policy_check(FriendListPolicy::class, 'viewAny', $context)) {
            return $this->success([]);
        }

        $params = $request->validated();

        $data = $this->repository->viewFriendLists($context, $params);

        return $this->success(new ItemCollection($data));
    }

    /**
     * Create friend list.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params     = $request->validated();
        $friendList = $this->repository->createFriendList(user(), $params['name']);
        $message    = __p(
            'core::phrase.resource_create_success',
            ['resource_name' => __p('friend::phrase.friend_list')]
        );

        return $this->success(new Detail($friendList), [], $message);
    }

    /**
     * View friend list.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->repository->viewFriendList(user(), $id);

        return $this->success(new Detail($data));
    }

    /**
     * Update friend list.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws ValidatorException
     * @throws AuthorizationException|AuthenticationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params     = $request->validated();
        $friendList = $this->repository->updateFriendList(user(), $id, $params['name']);

        return $this->success(new Detail($friendList), [], __p('friend::phrase.list_edited_successfully'));
    }

    /**
     * Remove friend list.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deleteFriendList(user(), $id);

        return $this->success([
            'id' => $id,
        ], [], __p('friend::phrase.list_deleted_successfully'));
    }

    /**
     * Add friend list.
     *
     * @param  ManageFriendListRequest $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function addFriendToList(ManageFriendListRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->repository->addFriendToFriendList(user(), $id, $params['user_ids']);

        return $this->success([], [], __p('friend::phrase.updated_friend_to_list_successfully'));
    }

    /**
     * Delete friend from list.
     *
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function deleteFriendFromList(int $id): JsonResponse
    {
        $this->repository->removeFriendFromFriendList(user(), $id, ['user_ids']);

        return $this->success([], [], __p('friend::phrase.remove_friend_from_list_successfully'));
    }

    // Not used yet

    /**
     * Assign friend to list.
     *
     * @param int $userId
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function getAssigned(int $userId): JsonResponse
    {
        $data = $this->repository->getAssignedListIds(user()->entityId(), $userId);

        return $this->success($data);
    }

    /**
     * Assign multiple friend to list.
     *
     * @param AssignFriendListRequest $request
     * @param int                     $userId
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    // Not used yet
    public function setAssigned(AssignFriendListRequest $request, int $userId): JsonResponse
    {
        $viewer = user();
        $params = $request->validated();
        $array1 = $this->repository->getAssignedListIds($viewer->id, $userId);
        $array2 = $params['list_id'];

        /** @var int[] $removeId */
        $removeId = array_diff($array1, $array2);

        /** @var int[] $appendId */
        $appendId = array_diff($array2, $array1);

        foreach ($removeId as $listId) {
            $this->repository->removeFriendFromFriendList($viewer, (int) $listId, [$userId]);
        }

        foreach ($appendId as $listId) {
            $this->repository->addFriendToFriendList($viewer, (int) $listId, [$userId]);
        }

        return $this->success([
            $removeId,
            $appendId,
        ], [], __p('friend::phrase.updated_friend_to_list_successfully'));
    }

    public function updateToFriendList(ManageFriendListRequest $request, int $id): JsonResponse
    {
        $data   = $request->validated();
        $userId = $data['user_ids'];
        $this->repository->updateToFriendList($id, $userId);

        return $this->success();
    }

    public function create()
    {
        return new CreateFriendListForm();
    }

    public function edit($id)
    {
        $resource = $this->repository->find($id);

        return new EditFriendListForm($resource);
    }
}
