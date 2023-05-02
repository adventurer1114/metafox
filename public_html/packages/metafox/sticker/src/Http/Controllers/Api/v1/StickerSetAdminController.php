<?php

namespace MetaFox\Sticker\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\ActiveRequest;
use MetaFox\Sticker\Http\Requests\v1\StickerSet\Admin\IndexRequest;
use MetaFox\Sticker\Http\Requests\v1\StickerSet\Admin\StoreRequest;
use MetaFox\Sticker\Http\Requests\v1\StickerSet\Admin\UpdateRequest;
use MetaFox\Sticker\Http\Resources\v1\StickerSet\Admin\StickerSetDetail as Detail;
use MetaFox\Sticker\Http\Resources\v1\StickerSet\Admin\StickerSetItemCollection as ItemCollection;
use MetaFox\Sticker\Http\Resources\v1\StickerSet\Admin\StoreStickerSetForm;
use MetaFox\Sticker\Http\Resources\v1\StickerSet\Admin\UpdateStickerSetForm;
use MetaFox\Sticker\Repositories\StickerSetRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Sticker\Http\Controllers\Api\StickerSetAdminController::$controllers;.
 */

/**
 * Class StickerSetAdminController.
 * @codeCoverageIgnore
 * @ignore
 * @group sticker
 */
class StickerSetAdminController extends ApiController
{
    /**
     * @var StickerSetRepositoryInterface
     */
    private StickerSetRepositoryInterface $repository;

    /**
     * StickerSetAdminController Constructor.
     *
     * @param StickerSetRepositoryInterface $repository
     */
    public function __construct(StickerSetRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse item.
     *
     * @param  IndexRequest                                     $request
     * @return JsonResponse
     * @throws AuthorizationException | AuthenticationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->viewStickerSetsForAdmin(user(), $params);

        return $this->success(new ItemCollection($data));
    }

    /**
     * Store item.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidatorException
     * @todo: should implement this later
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->installStickerSet(user(), $params);

        return $this->success(new Detail($data), [
            'nextAction' => [
                'type'    => 'navigate',
                'payload' => [
                    'url' => '/admincp/sticker/sticker-set/browse',
                ],
            ],
        ], __p('sticker::phrase.successfully_created_sticker'));
    }

    /**
     * View item.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     * @todo: implement this later??
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->repository->viewStickerSet(user(), $id);

        return $this->success(new Detail($data));
    }

    /**
     * Update item.
     *
     * @param  UpdateRequest           $request
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->updateStickerSet(user(), $id, $params);

        return $this->success(new Detail($data));
    }

    /**
     * Delete item.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deleteStickerSet(user(), $id);

        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function toggleActive(ActiveRequest $request, int $id): JsonResponse
    {
        $isActive = $request->get('active');
        $this->repository->updateActive(user(), $id, $isActive);

        return $this->success([
            'is_active' => $isActive,
        ], [], __p('core::phrase.updated_successfully'));
    }

    public function edit($id): JsonResponse
    {
        $item = $this->repository->find($id);

        return $this->success(new UpdateStickerSetForm($item));
    }

    public function create(): StoreStickerSetForm
    {
        return new StoreStickerSetForm();
    }
}
