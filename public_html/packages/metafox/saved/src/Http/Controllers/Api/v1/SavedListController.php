<?php

namespace MetaFox\Saved\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Saved\Http\Requests\v1\SavedList\IndexRequest;
use MetaFox\Saved\Http\Requests\v1\SavedList\ManageFriendListRequest;
use MetaFox\Saved\Http\Requests\v1\SavedList\StoreRequest;
use MetaFox\Saved\Http\Requests\v1\SavedList\UpdateRequest;
use MetaFox\Saved\Http\Requests\v1\SavedListMember\RemoveMemberRequest;
use MetaFox\Saved\Http\Resources\v1\SavedList\SavedListDataItemCollection;
use MetaFox\Saved\Http\Resources\v1\SavedList\SavedListDetail as Detail;
use MetaFox\Saved\Http\Resources\v1\SavedList\SavedListItemCollection as ItemCollection;
use MetaFox\Saved\Http\Resources\v1\SavedList\StoreSavedListForm;
use MetaFox\Saved\Http\Resources\v1\SavedList\UpdateSavedListForm;
use MetaFox\Saved\Http\Resources\v1\SavedListMember\MemberItemCollection;
use MetaFox\Saved\Models\SavedList;
use MetaFox\Saved\Policies\SavedListPolicy;
use MetaFox\Saved\Repositories\SavedListRepositoryInterface;

/**
 * Class SavedListController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group saved
 */
class SavedListController extends ApiController
{
    /**
     * @var SavedListRepositoryInterface
     */
    private SavedListRepositoryInterface $repository;

    /**
     * SavedListController Constructor.
     *
     * @param SavedListRepositoryInterface $repository
     */
    public function __construct(SavedListRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse list.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->viewSavedLists(user(), $params);

        return $this->success(new ItemCollection($data));
    }

    /**
     * Create list.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->createSavedList(user(), $params);

        return $this->success(new Detail($data), [], __p('saved::phrase.collection_successfully_created'));
    }

    /**
     * View list.
     *
     * @param IndexRequest $request
     * @param int          $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->repository->viewSavedList(user(), $id);

        return $this->success(new Detail($data));
    }

    /**
     * Update list.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->updateSavedList(user(), $id, $params);

        return $this->success(new Detail($data), [], __p('saved::phrase.collection_successfully_updated'));
    }

    /**
     * Remove list.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deleteSavedList(user(), $id);

        return $this->success([
            'id' => $id,
        ], [], __p('saved::phrase.collection_successfully_deleted'));
    }

    /**
     * View creation form.
     *
     * @return JsonResponse
     */
    public function formStore(): JsonResponse
    {
        $context = user();

        policy_authorize(SavedListPolicy::class, 'create', $context, null);

        return $this->success(new StoreSavedListForm(), []);
    }

    /**
     * View update form.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function formUpdate(int $id): JsonResponse
    {
        $savedList = new SavedList();
        $context   = user();

        $savedList = $this->repository->find($id);
        policy_authorize(SavedListPolicy::class, 'update', $context, $savedList);

        return $this->success(new UpdateSavedListForm($savedList), [], '');
    }

    public function addFriends(ManageFriendListRequest $request, int $id)
    {
        $params = $request->validated();
        $this->repository->addFriendToSavedList(user(), $id, $params['user_ids']);

        return $this->success([], [], __p('saved::phrase.updated_friend_to_list_successfully'));
    }

    public function viewFriends(int $id): JsonResponse
    {
        $members = $this->repository->viewSavedListMembers(user(), $id);

        return $this->success(new MemberItemCollection($members));
    }

    public function removeMember(RemoveMemberRequest $request, int $id): JsonResponse
    {
        $context = user();
        $params  = $request->validated();
        $deleted = $this->repository->removeMember($context, $id, $params);

        if ($deleted) {
            return $this->success([], [], __p('saved::phrase.remove_member_successfully'));
        }

        return $this->error(__p('saved::phrase.cannot_remove_this_member'));
    }

    public function leaveCollection(int $id)
    {
        $context = user();

        $leaved = $this->repository->leaveCollection($context, $id);

        if ($leaved) {
            return $this->success([], [], __p('saved::phrase.leave_collection_successfully'));
        }

        return $this->error(__p('saved::phrase.action_cannot_be_done'));
    }

    /**
     * @param  IndexRequest            $request
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function viewItemCollection(IndexRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        Arr::set($params, 'id', $id);
        $data = $this->repository->viewItemCollection(user(), $params);

        return $this->success(new SavedListDataItemCollection($data));
    }
}
