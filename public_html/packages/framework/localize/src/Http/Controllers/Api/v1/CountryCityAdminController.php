<?php

namespace MetaFox\Localize\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Localize\Http\Requests\v1\CountryCity\Admin\IndexRequest;
use MetaFox\Localize\Http\Requests\v1\CountryCity\Admin\StoreRequest;
use MetaFox\Localize\Http\Requests\v1\CountryCity\Admin\UpdateRequest;
use MetaFox\Localize\Http\Resources\v1\CountryCity\Admin\CountryCityDetail as Detail;
use MetaFox\Localize\Http\Resources\v1\CountryCity\Admin\CountryCityItemCollection as ItemCollection;
use MetaFox\Localize\Models\CountryChild;
use MetaFox\Localize\Repositories\CountryCityRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Core\Http\Controllers\Api\CountryCityAdminController::$controllers.
 */

/**
 * Class CountryCityAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class CountryCityAdminController extends ApiController
{
    /**
     * @var CountryCityRepositoryInterface
     */
    private CountryCityRepositoryInterface $repository;

    /**
     * CountryCityAdminController Constructor.
     *
     * @param CountryCityRepositoryInterface $repository
     */
    public function __construct(CountryCityRepositoryInterface $repository)
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

        $query = $this->repository->getModel()->newQuery();

        if ($stateId = $params['state_id'] ?? null) {
            $state = CountryChild::find($stateId);
            $query = $query->where('state_code', '=', $state->state_code);
        }

        $data = $query->paginate($params['limit'] ?? 100);

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
    public function show(int $id): Detail
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

//    /**
//     * Get creation form
//     *
//     * @return StoreCountryCityForm
//     */
//    public function create(): StoreCountryCityForm
//    {
//        return new StoreCountryCityForm();
//    }
//
//    /**
//     * Get updating form
//     *
//     * @param  int  $id
//     *
//     * @return UpdateCountryCityForm
//     */
//    public function edit(int $id): UpdateCountryCityForm
//    {
//        $resource = $this->repository->find($id);
//
//        return new UpdateCountryCityForm($resource);
//    }
//    /**
//     * Get updating form
//     *
//     * @param  int  $id
//     *
//     * @return DestroyCountryCityForm
//     */
//    public function getDestroyForm(int $id): DestroyCountryCityForm
//    {
//        $resource = $this->repository->find($id);
//
//        return new DestroyCountryCityForm($resource);
//    }
}
