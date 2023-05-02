<?php

namespace MetaFox\Storage\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Storage\Http\Requests\v1\Asset\Admin\IndexRequest;
use MetaFox\Storage\Http\Requests\v1\Asset\Admin\StoreRequest;
use MetaFox\Storage\Http\Requests\v1\Asset\Admin\UpdateRequest;
use MetaFox\Storage\Http\Resources\v1\Asset\Admin\AssetItem;
use MetaFox\Storage\Http\Resources\v1\Asset\Admin\AssetItem as Detail;
use MetaFox\Storage\Http\Resources\v1\Asset\Admin\AssetItemCollection as ItemCollection;
use MetaFox\Storage\Http\Resources\v1\Asset\Admin\EditAssetForm;
use MetaFox\Storage\Models\Asset;
use MetaFox\Storage\Repositories\AssetRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Storage\Http\Controllers\Api\AssetAdminController::$controllers.
 */

/**
 * Class AssetAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class AssetAdminController extends ApiController
{
    /**
     * @var AssetRepositoryInterface
     */
    private AssetRepositoryInterface $repository;

    /**
     * AssetAdminController Constructor.
     *
     * @param AssetRepositoryInterface $repository
     */
    public function __construct(AssetRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse item.
     *
     * @param  IndexRequest $request
     * @return mixed
     */
    public function index(IndexRequest $request): ItemCollection
    {
        $params = $request->validated();
        $data   = $this->repository->paginate($params['limit'] ?? 50);

        return new ItemCollection($data);
    }

    /**
     * Store item.
     *
     * @param StoreRequest $request
     *
     * @return
     * @throws ValidatorException
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->create($params);

        return new Detail($data);
    }

    /**
     * View item.
     *
     * @param int $id
     *
     * @return Detail
     */
    public function show($id): Detail
    {
        $data = $this->repository->find($id);

        return new Detail($data);
    }

    /**
     * Update item.
     *
     * @param  UpdateRequest      $request
     * @param  int                $id
     * @return Detail
     * @throws ValidatorException
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->update($params, $id);

        return new Detail($data);
    }

    public function edit(int $id): JsonResponse
    {
        $asset = $this->repository->find($id);

        return $this->success(new EditAssetForm($asset));
    }

    public function upload(int $id, Request $request): JsonResponse
    {
        /** @var Asset $asset */
        $asset = $this->repository->find($id);

        $file = $request->file('file');

        $name = app('storage.path')->fileName($file->extension());

        $storageFile = app('storage')->putFileAs('asset', 'asset', $file, $name);

        $asset->file_id = $storageFile->id;

        $asset->save();

        return $this->success(new AssetItem($asset));
    }

    /**
     * Delete item.
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
