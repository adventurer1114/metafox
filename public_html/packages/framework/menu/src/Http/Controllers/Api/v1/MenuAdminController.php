<?php

namespace MetaFox\Menu\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Menu\Http\Requests\v1\Menu\Admin\IndexRequest;
use MetaFox\Menu\Http\Requests\v1\Menu\Admin\StoreRequest;
use MetaFox\Menu\Http\Requests\v1\Menu\Admin\UpdateRequest;
use MetaFox\Menu\Http\Resources\v1\Menu\Admin\MenuDetail as Detail;
use MetaFox\Menu\Http\Resources\v1\Menu\Admin\MenuItemCollection as ItemCollection;
use MetaFox\Menu\Http\Resources\v1\Menu\Admin\StoreMenuForm;
use MetaFox\Menu\Http\Resources\v1\Menu\Admin\UpdateMenuForm;
use MetaFox\Menu\Models\Menu;
use MetaFox\Menu\Repositories\Eloquent\MenuItemRepository;
use MetaFox\Menu\Repositories\MenuRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\ActiveRequest;
use MetaFox\Platform\PackageManager;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Core\Http\Controllers\Api\MenuAdminController::$controllers.
 */

/**
 * Class MenuAdminController.
 * @group admin/menu
 * @authenticated
 * @ignore
 */
class MenuAdminController extends ApiController
{
    /**
     * @var MenuRepositoryInterface
     */
    private MenuRepositoryInterface $repository;

    /**
     * @param MenuRepositoryInterface $repository
     */
    public function __construct(MenuRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse menu.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();

        $query = $this->repository->getModel()->newQuery()->orderBy('name');

        if ($search = $params['q'] ?? null) {
            $query = $query->addScope(new SearchScope($search, ['name', 'title']));
        }

        if ($packageId = $params['package_id'] ?? null) {
            $query = $query->where('package_id', $packageId);
        }

        if ($resolution = $params['resolution'] ?? 'web') {
            $query->where('resolution', '=', $resolution);
        }

        if ($type = $params['type'] ?? 'site') {
            $query->where('type', '=', $type);
        }

        $collection = new ItemCollection($query->paginate(50));

        return $collection->toResponse($request);
    }

    /**
     * Create menu.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();

        $data       = $this->repository->create($params);
        $nextAction = ['type' => 'navigate', 'payload' => ['url' => '/admincp/menu/menu/browse']];

        return $this->success(new Detail($data), ['nextAction' => $nextAction]);
    }

    /**
     * View menu.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->repository->find($id);

        return new JsonResponse(new Detail($data));
    }

    /**
     * Update menu.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $data       = $this->repository->update($params, $id);
        $nextAction = ['type' => 'navigate', 'payload' => ['url' => '/admincp/menu/menu/browse']];

        return $this->success(new Detail($data), ['nextAction' => $nextAction]);
    }

    /**
     * Update active status.
     *
     * @param ActiveRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function toggleActive(ActiveRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        /** @var Menu $resource */
        $resource = $this->repository->update([
            'is_active' => $params['active'],
        ], $id);

        return $this->success(new Detail($resource));
    }

    public function create(): JsonResponse
    {
        return $this->success(new StoreMenuForm());
    }

    public function edit(int $id): JsonResponse
    {
        $model = $this->repository->findOrFail($id);

        $form = new UpdateMenuForm($model);

        return $this->success($form);
    }

    /**
     * Delete menu.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $resource = $this->repository->find($id);

        $resource->delete();

        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * Show Menu.
     *
     * @param string $menuName
     *
     * @return JsonResponse
     */
    public function showMenu(string $menuName): JsonResponse
    {
        $data = resolve(MenuItemRepository::class)
            ->loadItems($menuName, 'admin');

        return $this->success($data);
    }
}
