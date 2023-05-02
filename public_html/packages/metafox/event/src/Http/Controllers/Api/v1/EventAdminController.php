<?php

namespace MetaFox\Event\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Event\Http\Requests\v1\Event\Admin\IndexRequest;
use MetaFox\Event\Http\Requests\v1\Event\Admin\StoreRequest;
use MetaFox\Event\Http\Requests\v1\Event\Admin\UpdateRequest;
use MetaFox\Event\Http\Resources\v1\Event\Admin\EventDetail as Detail;
use MetaFox\Event\Http\Resources\v1\Event\Admin\EventItemCollection as ItemCollection;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Event\Http\Controllers\Api\EventAdminController::$controllers;
 */

/**
 * Class EventAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group event
 * @admincp
 */
class EventAdminController extends ApiController
{
    /**
     * @var EventRepositoryInterface
     */
    public $repository;

    public function __construct(EventRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest $request
     * @return mixed
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();
        $data   = $this->repository->get($params);

        return new ItemCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     */
    public function store(StoreRequest $request)
    {
        $params = $request->validated();
        $data   = $this->repository->create($params);

        return new Detail($data);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Detail
     */
    public function show($id)
    {
        $data = $this->repository->find($id);

        return new Detail($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest      $request
     * @param  int                $id
     * @return Detail
     * @throws ValidatorException
     */
    public function update(UpdateRequest $request, $id)
    {
        $params = $request->validated();
        $data   = $this->repository->update($params, $id);

        return new Detail($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return $this->success([
            'id' => $id,
        ]);
    }
}
