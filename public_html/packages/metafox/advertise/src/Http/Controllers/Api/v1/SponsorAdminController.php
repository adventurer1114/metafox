<?php

namespace MetaFox\Advertise\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Advertise\Http\Resources\v1\Sponsor\Admin\SponsorItemCollection as ItemCollection;
use MetaFox\Advertise\Http\Resources\v1\Sponsor\Admin\SponsorDetail as Detail;
use MetaFox\Advertise\Repositories\SponsorRepositoryInterface;
use MetaFox\Advertise\Http\Requests\v1\Sponsor\Admin\IndexRequest;
use MetaFox\Advertise\Http\Requests\v1\Sponsor\Admin\StoreRequest;
use MetaFox\Advertise\Http\Requests\v1\Sponsor\Admin\UpdateRequest;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Advertise\Http\Controllers\Api\SponsorAdminController::$controllers;
 */

/**
 * Class SponsorAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class SponsorAdminController extends ApiController
{
    /**
     * @var SponsorRepositoryInterface
     */
    private SponsorRepositoryInterface $repository;

    /**
     * SponsorAdminController Constructor.
     *
     * @param SponsorRepositoryInterface $repository
     */
    public function __construct(SponsorRepositoryInterface $repository)
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
        $data   = $this->repository->paginate($params['limit'] ?? 100);

        return new ItemCollection($data);
    }

    /**
     * Store item.
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
