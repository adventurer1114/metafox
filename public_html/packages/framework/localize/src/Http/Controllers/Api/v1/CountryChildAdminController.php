<?php

namespace MetaFox\Localize\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Localize\Http\Requests\v1\CountryChild\Admin\DeleteAllRequest;
use MetaFox\Localize\Http\Requests\v1\CountryChild\Admin\IndexRequest;
use MetaFox\Localize\Http\Requests\v1\CountryChild\Admin\StoreRequest;
use MetaFox\Localize\Http\Requests\v1\CountryChild\Admin\UpdateRequest;
use MetaFox\Localize\Http\Resources\v1\CountryChild\Admin\CountryChildDetail as Detail;
use MetaFox\Localize\Http\Resources\v1\CountryChild\Admin\CountryChildItem;
use MetaFox\Localize\Http\Resources\v1\CountryChild\Admin\CountryChildItemCollection as ItemCollection;
use MetaFox\Localize\Http\Resources\v1\CountryChild\Admin\StoreCountryChildForm;
use MetaFox\Localize\Repositories\CountryChildRepositoryInterface;
use MetaFox\Localize\Repositories\CountryRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\OrderingRequest;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class CountryChildController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 * @group admin/country/child
 * @authenticated
 */
class CountryChildAdminController extends ApiController
{
    public CountryChildRepositoryInterface $repository;

    public CountryRepositoryInterface $countries;

    /**
     * CountryChildAdminController constructor.
     *
     * @param CountryChildRepositoryInterface $repository
     * @param CountryRepositoryInterface      $countries
     *
     * @ignore
     */
    public function __construct(CountryChildRepositoryInterface $repository, CountryRepositoryInterface $countries)
    {
        $this->repository = $repository;
        $this->countries  = $countries;
    }

    /**
     * Browse country child.
     *
     * @param IndexRequest $request
     *
     * @return ItemCollection<CountryChildItem>
     * @group admin/country/child
     */
    public function index(IndexRequest $request): ItemCollection
    {
        $params = $request->validated();
        $query  = $this->repository->getModel()->newQuery();

        if ($countryId = $params['country_id'] ?? null) {
            $country = $this->countries->find($countryId);
            $query   = $query->where(['country_iso' => $country->country_iso]);
        }

        return new ItemCollection($query->paginate($params['limit'] ?? 50));
    }

    /**
     * Create country child.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     * @throws AuthorizationException|AuthenticationException
     * @group admin/country/child
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->createCountryChild(user(), $params);

        return new Detail($data);
    }

    /**
     * View country child.
     *
     * @param int $id
     *
     * @return Detail
     * @throws AuthorizationException|AuthenticationException
     * @group admin/country/child
     * @ignore
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->viewCountryChild(user(), $id);

        return new Detail($data);
    }

    /**
     * Update country child.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return Detail
     * @throws AuthorizationException|AuthenticationException
     * @group admin/country/child
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->updateCountryChild(user(), $id, $params);

        return new Detail($data);
    }

    /**
     * Delete country child.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     * @ignore
     * @group admin/country/child
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deleteCountryChild(user(), $id);

        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * Update order.
     *
     * @param OrderingRequest $request
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     * @ignore
     * @group admin/country/child
     */
    public function order(OrderingRequest $request): JsonResponse
    {
        $params = $request->validated();
        $this->repository->orderCountryChildren(user(), $params['orders']);

        return $this->success();
    }

    /**
     * Batch delete countries.
     *
     * @param DeleteAllRequest $request
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException|AuthenticationException
     * @ignore
     * @group admin/country/child
     */
    public function deleteAll(DeleteAllRequest $request): JsonResponse
    {
        $params     = $request->validated();
        $countryId  = $params['country_id'] ?? 0;
        $countryIso = $params['country_iso'] ?? '';
        $usingId    = (bool) $countryId;
        $this->repository->deleteAllChildren(user(), $params, $usingId, $usingId ? $countryId : $countryIso);

        return $this->success();
    }

    /**
     * View creation form.
     *
     * @param Request $request
     *
     * @return JsonResource
     * @ignore
     * @group admin/country/child
     */
    public function create(Request $request)
    {
        $id = $request->get('country_id');

        $country = $this->countries->find($id);

        return new StoreCountryChildForm($country);
    }

    /**
     * View editing form.
     *
     * @param int $id
     *
     * @return JsonResource
     * @ignore
     * @group admin/country/child
     */
    public function edit(int $id): JsonResource
    {
        $resource = $this->repository->find($id);

        return new StoreCountryChildForm($resource);
    }
}
