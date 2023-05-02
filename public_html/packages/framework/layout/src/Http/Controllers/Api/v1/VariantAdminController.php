<?php

namespace MetaFox\Layout\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MetaFox\Layout\Http\Requests\v1\Variant\Admin\IndexRequest;
use MetaFox\Layout\Http\Requests\v1\Variant\Admin\StoreRequest;
use MetaFox\Layout\Http\Requests\v1\Variant\Admin\UpdateRequest;
use MetaFox\Layout\Http\Resources\v1\Variant\Admin\CreateVariantForm;
use MetaFox\Layout\Http\Resources\v1\Variant\Admin\EditVariantForm;
use MetaFox\Layout\Http\Resources\v1\Variant\Admin\VariantDetail as Detail;
use MetaFox\Layout\Http\Resources\v1\Variant\Admin\VariantItemCollection as ItemCollection;
use MetaFox\Layout\Models\Variant;
use MetaFox\Layout\Models\Theme;
use MetaFox\Layout\Repositories\VariantRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\ActiveRequest;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Layout\Http\Controllers\Api\VariantAdminController::$controllers;.
 */

/**
 * class VariantAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class VariantAdminController extends ApiController
{
    /**
     * @var VariantRepositoryInterface
     */
    private VariantRepositoryInterface $repository;

    /**
     * VariantAdminController Constructor.
     *
     * @param VariantRepositoryInterface $repository
     */
    public function __construct(VariantRepositoryInterface $repository)
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
        $query   = $this->repository->getModel()->newQuery();
        $themeId = $request->input('theme_id', 1);
        $params  = $request->validated();
        $theme   = Theme::find($themeId);
        $data    = $query->where('theme_id', '=', $theme->theme_id)
            ->orderBy('title', 'asc')
            ->paginate($params['limit'] ?? 100);

        return new ItemCollection($data);
    }

    public function create(Request $request): JsonResponse
    {
        $themeId = $request->input('theme_id');

        $theme = Theme::query()->where('theme_id', '=', $themeId)->firstOrFail();

        return $this->success(new CreateVariantForm($theme));
    }

    public function edit(int $id)
    {
        $variant = $this->repository->find($id);

        return $this->success(new EditVariantForm($variant));
    }

    /**
     * Store item.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();

        /** @var Variant $data */
        $data = $this->repository->create($params);

        $this->navigate('/admincp/layout/variant/browse?theme_id=' . $data->theme_id);

        return $this->success(new Detail($data));
    }

    public function toggleActive(ActiveRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        /** @var Variant $variant */
        $variant            = $this->repository->find($id);
        $variant->is_active = $params['active'] ?? 1;
        $variant->save();

        return $this->success([
            'id'        => $id,
            'is_active' => (int) $params['active'],
        ]);
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
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->update($params, $id);

        return $this->success(new Detail($data));
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
        /** @var Variant $variant */
        $variant = $this->repository->find($id);

        if ($variant->is_system) {
            throw new \InvalidArgumentException('Failed deleting system theme variant');
        }

        $variant->delete();

        return $this->success([
            'id' => $id,
        ]);
    }
}
