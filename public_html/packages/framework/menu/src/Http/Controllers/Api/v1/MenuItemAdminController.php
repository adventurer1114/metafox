<?php

namespace MetaFox\Menu\Http\Controllers\Api\v1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use MetaFox\Menu\Http\Requests\v1\MenuItem\Admin\IndexRequest;
use MetaFox\Menu\Http\Requests\v1\MenuItem\Admin\StoreRequest;
use MetaFox\Menu\Http\Requests\v1\MenuItem\Admin\UpdateRequest;
use MetaFox\Menu\Http\Resources\v1\MenuItem\Admin\MenuItemDetail as Detail;
use MetaFox\Menu\Http\Resources\v1\MenuItem\Admin\MenuItemItemCollection as ItemCollection;
use MetaFox\Menu\Http\Resources\v1\MenuItem\Admin\StoreMenuItemForm;
use MetaFox\Menu\Http\Resources\v1\MenuItem\Admin\UpdateMenuItemForm;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\ActiveRequest;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Core\Http\Controllers\Api\MenuItemAdminController::$controllers.
 */

/**
 * Class MenuItemAdminController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @group admin/menu/item
 * @ignore
 * @authenticated
 */
class MenuItemAdminController extends ApiController
{
    /**
     * @var MenuItemRepositoryInterface
     */
    private MenuItemRepositoryInterface $repository;

    /**
     * @param MenuItemRepositoryInterface $repository
     */
    public function __construct(MenuItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retry menu items.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     * @group admin/menu
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params     = $request->validated();
        $search     = Arr::get($params, 'q');
        $menuName   = Arr::get($params, 'menu');
        $packageId  = Arr::get($params, 'package_id');
        $resolution = Arr::get($params, 'resolution');

        $query = $this->repository->getModel()->newQuery();

        if ($search) {
            $query = $query->addScope(new SearchScope($search, ['name', 'menu', 'label']));
        }

        if ($menuName) {
            $query = $query->where('menu', '=', $menuName);
        }

        if ($packageId) {
            $query = $query->where('package_id', '=', $packageId);
        }

        if ($resolution) {
            $query = $query->where('resolution', '=', $resolution);
        }

        $query = $query
            ->select(['*', DB::raw('CASE WHEN parent_name IS NULL THEN 0 ELSE 1 END AS sort_null')])
            ->orderByRaw('sort_null')
            ->orderBy('parent_name')
            ->orderBy('ordering')
            ->orderBy('label');

        $allowPagination = $this->enablePaginationForBrowse($menuName);

        $items = match ($allowPagination) {
            true  => $query->paginate(50),
            false => $query->limit(100)->get(),
        };

        $collection = new ItemCollection($items);

        return $collection->toResponse($request);
    }

    protected function enablePaginationForBrowse(?string $menu): bool
    {
        if (null === $menu) {
            return false;
        }

        return in_array($menu, ['core.adminSidebarMenu']);
    }

    /**
     * Create menu item.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @group admin/menu
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->createMenuItem($params);

        $nextAction = [
            'type'    => 'navigate',
            'payload' => [
                'url' => sprintf('admincp/menu/menu-item/browse?menu=%s&resolution=%s', $data->menu, $data->resolution),
            ],
        ];

        return $this->success(new Detail($data), ['nextAction' => $nextAction], __p('menu::phrase.menu_item_created_successfully'));
    }

    /**
     * View menu item.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @group admin/menu
     */
    public function show(int $id): JsonResponse
    {
        $menuItem = $this->repository->find($id);

        return $this->success(new Detail($menuItem));
    }

    /**
     * Update menu item.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @group admin/menu
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->updateMenuItem($id, $params);

        return $this->success(new Detail($data));
    }

    /**
     * Delete menu item.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @group admin/menu
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);

        return $this->success([], [], __p('core::phrase.already_saved_changes'));
    }

    /**
     * Get the creation form.
     *
     * @param int|null $id
     *
     * @return JsonResponse
     * @group admin/menu
     */
    public function edit(int $id = null): JsonResponse
    {
        $menuItem = $this->repository->find($id);

        return $this->success(new UpdateMenuItemForm($menuItem));
    }

    /**
     * Get the creation form.
     *
     *
     * @return JsonResponse
     * @group admin/menu
     */
    public function create(): JsonResponse
    {
        return $this->success(new StoreMenuItemForm());
    }

    /**
     * Active menu item.
     *
     * @param ActiveRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws ValidatorException
     * @group admin/menu
     */
    public function toggleActive(ActiveRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $resource = $this->repository->update([
            'is_active' => $params['active'],
        ], $id);

        return $this->success(new Detail($resource));
    }

    public function order(Request $request): JsonResponse
    {
        $orderIds = $request->get('order_ids');

        $this->repository->orderItems($orderIds);

        return $this->success([], [], __p('menu::phrase.menus_successfully_ordered'));
    }
}
