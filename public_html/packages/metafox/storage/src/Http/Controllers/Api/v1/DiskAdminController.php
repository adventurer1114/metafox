<?php

namespace MetaFox\Storage\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Storage\Http\Requests\v1\Disk\Admin\StoreRequest;
use MetaFox\Storage\Http\Requests\v1\Disk\Admin\UpdateRequest;
use MetaFox\Storage\Http\Resources\v1\Disk\Admin\DiskItem;
use MetaFox\Storage\Http\Resources\v1\Disk\Admin\DiskItemCollection as ItemCollection;
use MetaFox\Storage\Http\Resources\v1\Disk\Admin\StoreDiskForm;
use MetaFox\Storage\Http\Resources\v1\Disk\Admin\UpdateDiskForm;
use MetaFox\Storage\Models\Disk;
use MetaFox\Storage\Repositories\DiskRepositoryInterface;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Storage\Http\Controllers\Api\DiskAdminController::$controllers.
 */

/**
 * Class DiskAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class DiskAdminController extends ApiController
{
    private DiskRepositoryInterface $diskRepository;

    /**
     * @param DiskRepositoryInterface $diskRepository
     */
    public function __construct(DiskRepositoryInterface $diskRepository)
    {
        $this->diskRepository = $diskRepository;
    }

    /**
     * Browse item.
     *
     * @return mixed
     */
    public function index(): JsonResponse
    {
        $result = $this->diskRepository->get();

        return $this->success(new ItemCollection($result));
    }

    /**
     * Delete item.
     *
     * @param int $disk
     *
     * @return JsonResponse
     */
    public function destroy(int $disk): JsonResponse
    {
        $this->diskRepository->delete($disk);

        // try to destroy disk
        return $this->success([
            'id' => $disk,
        ]);
    }

    public function create(): JsonResponse
    {
        $form = new StoreDiskForm();

        return $this->success($form);
    }

    public function edit(mixed $disk): JsonResponse
    {
        $item = $this->diskRepository->find($disk);

        $form = new UpdateDiskForm($item);

        return $this->success($form);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $disk = $this->diskRepository->create($data);

        return $this->success(new DiskItem($disk));
    }

    public function update(int $disk, UpdateRequest $request): JsonResponse
    {
        /** @var Disk $item */
        $item = $this->diskRepository->find($disk);

        $item->fill($request->validated());

        $item->save();

        return $this->success(new DiskItem($item));
    }
}
