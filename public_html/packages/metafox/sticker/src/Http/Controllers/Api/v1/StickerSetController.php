<?php

namespace MetaFox\Sticker\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\ActiveRequest;
use MetaFox\Platform\Http\Requests\v1\OrderingRequest;
use MetaFox\Sticker\Http\Requests\v1\StickerSet\GetStickersRequest;
use MetaFox\Sticker\Http\Requests\v1\StickerSet\IndexRequest;
use MetaFox\Sticker\Http\Requests\v1\StickerSet\StoreRequest;
use MetaFox\Sticker\Http\Requests\v1\StickerSet\UpdateRequest;
use MetaFox\Sticker\Http\Resources\v1\StickerSet\StickerSetDetail as Detail;
use MetaFox\Sticker\Http\Resources\v1\StickerSet\StickerSetItemCollection as ItemCollection;
use MetaFox\Sticker\Repositories\StickerSetRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class StickerSetController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group sticker
 */
class StickerSetController extends ApiController
{
    /**
     * @var StickerSetRepositoryInterface
     */
    private StickerSetRepositoryInterface $repository;

    /**
     * @param StickerSetRepositoryInterface $repository
     */
    public function __construct(StickerSetRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse sticker.
     *
     * @param IndexRequest $request
     *
     * @return JsonResource
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();
        $data   = $this->repository->viewStickerSetsForFE(user(), $params);

        return new ItemCollection($data);
    }

    /**
     * View sticker.
     *
     * @param IndexRequest $request
     *
     * @return JsonResource
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function viewStickerSetsForFE(IndexRequest $request)
    {
        $params = $request->validated();
        $data   = $this->repository->viewStickerSetsForFE(user(), $params);

        return new ItemCollection($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @param int          $id
     *
     * @return JsonResource
     * @throws AuthorizationException
     */
    public function viewStickerSetsUserForFE(IndexRequest $request, int $id)
    {
        $user = UserEntity::getById($id)->detail;

        $params = $request->validated();
        $data   = $this->repository->viewStickerSetsUserForFE($user, $params);

        return new ItemCollection($data);
    }

    /**
     * Create new sticker set.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     * @throws AuthorizationException|AuthenticationException
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->createStickerSet(user(), $params);

        return new Detail($data);
    }

    /**
     * View sticker set.
     *
     * @param int $id
     *
     * @return Detail
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->viewStickerSet(user(), $id);

        return new Detail($data);
    }

    /**
     * Update sticker set.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return Detail
     * @throws AuthenticationException|AuthorizationException
     * @throws ValidationException
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->updateStickerSet(user(), $id, $params);

        return new Detail($data);
    }

    /**
     * Delete sticker set.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException|ValidationException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deleteStickerSet(user(), $id);

        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * Active sticker set.
     *
     * @param  ActiveRequest           $request
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function updateActive(ActiveRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $this->repository->updateActive(user(), $id, $params['is_active']);

        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function addUserStickerSet(int $id): JsonResponse
    {
        $this->repository->addUserStickerSet(user(), $id);

        return $this->success();
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function deleteUserStickerSet(int $id): JsonResponse
    {
        $this->repository->deleteUserStickerSet(user(), $id);

        return $this->success();
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function markAsDefault(int $id): JsonResponse
    {
        $this->repository->markAsDefault(user(), $id);

        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function removeDefault(int $id): JsonResponse
    {
        $this->repository->removeDefault(user(), $id);

        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * @param OrderingRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function orderingStickerSet(OrderingRequest $request): JsonResponse
    {
        $params = $request->validated();
        $this->repository->orderingStickerSet(user(), $params['orders']);

        return $this->success();
    }

    /**
     * @param OrderingRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function orderingSticker(OrderingRequest $request): JsonResponse
    {
        $params = $request->validated();
        $this->repository->orderingSticker(user(), $params['orders']);

        return $this->success();
    }
}
