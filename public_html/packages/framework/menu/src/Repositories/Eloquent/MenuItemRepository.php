<?php

namespace MetaFox\Menu\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MetaFox\Menu\Models\MenuItem;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Platform\PackageManager;
use MetaFox\Platform\Repositories\AbstractRepository;
use RuntimeException;

/**
 * @method MenuItem find($id, $columns = ['*'])
 * @method MenuItem getModel()
 */
class MenuItemRepository extends AbstractRepository implements MenuItemRepositoryInterface
{
    public const MARK_AS_DELETED = 'deleted';

    public function model(): string
    {
        return MenuItem::class;
    }

    public function getMenuItemByMenuName(string $menuName, string $resolution, bool $isActive = null): Collection
    {
        $query = $this->getModel()->newQuery()
            ->where(['menu' => $menuName, 'resolution' => $resolution]);

        if (isset($isActive)) {
            $query->where('is_active', '=', $isActive);
        }

        return $query->orderBy('parent_name', 'asc')
            ->orderBy('ordering', 'asc')
            ->orderBy('label', 'asc')
            ->get();
    }

    public function setupMenuItems(string $package, string $resolution, ?array $items): bool
    {
        if (!$items) {
            return true;
        }
        $packageId     = PackageManager::getName($package);
        $moduleId      = PackageManager::getAlias($package);
        $fields        = $this->getModel()->getFillable();
        $shouldDeletes = [];

        $inserts = [];

        foreach ($items as $item) {
            if ($item['is_deleted'] ?? false) {
                $shouldDeletes[] = [
                    'menu'       => $item['menu'] ?? '',
                    'resolution' => $resolution, 'parent_name' => $item['parent_name'] ?? '', 'name' => $item['name'],
                ];
                continue;
            }
            $item = array_merge([
                'module_id'   => $moduleId,
                'package_id'  => $packageId,
                'menu'        => '',
                'parent_name' => null,
                'name'        => '',
                'label'       => '',
                'note'        => null,
                'ordering'    => 0,
                'is_active'   => 1,
                'resolution'  => $resolution,
                'as'          => null,
                'icon'        => null,
                'testid'      => null,
                'value'       => null,
                'to'          => null,
            ], $item);

            if ($item['parent_name'] === null) {
                $item['parent_name'] = '';
            }

            $item['extra'] = json_encode(Arr::except($item, ['is_deleted', 'version', ...$fields]));
            $inserts[]     = Arr::only($item, $fields);
        }

        // dump duplicated key.
        $keys = [];
        foreach ($inserts as $item) {
            $spice = Arr::only($item, ['menu', 'resolution', 'parent_name', 'name']);
            $key   = implode('.', $spice);
            if (in_array($key, $keys)) {
                throw new RuntimeException('duplicated menu item ' . $key);
            }
            array_push($keys, $key);
        }

        $allows = $resolution === 'admin' ? null : ['package_id', 'as', 'extra', 'value'];
        MenuItem::query()->upsert(
            $inserts,
            ['menu', 'resolution', 'parent_name', 'name'],
            $allows
        );

        if ($shouldDeletes) {
            foreach ($shouldDeletes as $where) {
                MenuItem::query()->where($where)->delete();
            }
        }

        return true;
    }

    public function loadItems(string $menuName, string $resolution): array
    {
        $return = [];

        /** @var MenuItem[] $rows */
        $rows = $this->where([
            ['module_id', 'in', resolve('core.packages')->getActivePackageAliases()],
            'menu'       => $menuName,
            'resolution' => $resolution,
            'is_active'  => 1,
        ])->orderBy('parent_name')
            ->orderBy('ordering')
            ->orderBy('label')
            ->get();

        foreach ($rows as $row) {
            $data = array_trim_null(Arr::except(array_merge($row->toArray(), $row->extra), [
                // reduce response size
                'id',
                'is_active',
                'ordering',
                'version',
                'resolution',
                'menu',
                'extra',
                'created_at',
                'updated_at',
                'module_id',
                'package_id',
            ]), [
                'icon'        => '',
                'parent_name' => '',
                'as'          => '',
                'testid'      => '',
            ]);

            if ($row->label) {
                $data['label'] = __p($row->label);
            }

            if (isset($data['subInfo'])) {
                $data['subInfo'] = __p($data['subInfo']);
            }

            $return[$row->name] = $data;
        }

        // should drop null value.
        return $this->arrayToTree($return);
    }

    /**
     * @param array<string, mixed> $array
     *
     * @return array<string,mixed>
     */
    public function arrayToTree(array $array): array
    {
        $grouped = [];
        foreach ($array as $node) {
            $grouped[$node['parent_name'] ?? ''][] = $node;
        }

        $fnBuilder = function ($siblings) use (&$fnBuilder, $grouped) {
            foreach ($siblings as $k => $sibling) {
                $id = $sibling['name'];
                if (isset($grouped[$id])) {
                    $items            = $fnBuilder($grouped[$id]);
                    $sibling['items'] = $items;
                }
                $siblings[$k] = $sibling;
            }

            return $siblings;
        };

        if (!isset($grouped[''])) {
            return [];
        }

        return $fnBuilder($grouped['']);
    }

    public function createMenuItem(array $params): MenuItem
    {
        $fields        = $this->getModel()->getFillable();
        $data          = Arr::only($params, $fields);
        $data['extra'] = Arr::except($params, $fields);

        if (!@$data['name']) {
            $data['name'] = Str::lower(Str::slug($params['label'] ?? '_', '_'));
        }

        $menuItem = new MenuItem($data);

        $menuItem->save();

        return $menuItem;
    }

    public function updateMenuItem(int $id, array $params): MenuItem
    {
        $menuItem = $this->find($id);

        $fields = $this->getModel()->getFillable();
        $data   = Arr::only($params, $fields);
        $menuItem->update($data);

        return $menuItem->refresh();
    }

    public function dumpByPackage(string $package, string $resolution): array
    {
        $result   = [];
        $moduleId = PackageManager::getAlias($package);

        /** @var Collection<MenuItem> $allItems */
        $allItems = $this->getModel()->newQuery()
            ->where(['module_id' => $moduleId, 'resolution' => $resolution])
            ->orderBy('menu')
            ->orderBy('parent_name', 'desc')
            ->orderBy('ordering')
            ->orderBy('id')
            ->cursor();

        $excepts = [
            'id', 'created_at', 'resolution', 'menu_type', 'module_id', 'testid',
            'package_id', 'updated_at', 'extra', 'version', 'is_deleted',
        ];

        $strips = [
            'is_active'   => 1,
            'parent_name' => '',
            'version'     => 0,
        ];

        foreach ($allItems as $item) {
            $result[] = array_trim_null(
                Arr::except(array_merge($item->extra ?? [], $item->toArray()), $excepts),
                $strips
            );
        }

        return $result;
    }

    public function deleteMenuItem(array $attributes): bool
    {
        $item = $this->getModel()->newModelQuery()
            ->where($attributes)
            ->first();

        if (null === $item) {
            return true;
        }

        $item->delete();

        return true;
    }

    public function orderItems(array $orderIds): bool
    {
        $items = MenuItem::query()
            ->whereIn('id', $orderIds)
            ->get()
            ->filter(function ($item) {
                /*
                 * TODO: remove this filter when refactor layout
                 */
                return $item->menu != 'core.adminSidebarMenu';
            })
            ->keyBy('id');

        if (!$items->count()) {
            return true;
        }

        $ordering = 1;

        foreach ($orderIds as $orderId) {
            $orderItem = $items->get($orderId);

            if (null === $orderItem) {
                continue;
            }

            $orderItem->update(['ordering' => $ordering++]);
        }

        Artisan::call('cache:reset');

        return true;
    }

    public function getMenuItemByName(string $menu, string $name, string $resolution, ?string $parentName = null): ?MenuItem
    {
        return $this->getModel()->newQuery()
            ->where([
                'menu'        => $menu,
                'name'        => $name,
                'resolution'  => $resolution,
                'parent_name' => $parentName,
            ])
            ->first();
    }
}
