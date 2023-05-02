<?php

namespace MetaFox\Marketplace\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Marketplace\Repositories\CategoryRepositoryInterface;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;

/**
 * Class PackageSeeder.
 * @codeCoverageIgnore
 * @ignore
 */
class PackageSeeder extends Seeder
{
    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $repository;

    /**
     * PrivacyDatabaseSeeder constructor.
     *
     * @param CategoryRepositoryInterface $repository
     */
    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->categories();

        $this->removeUnusedMenuItems();
    }

    private function categories()
    {
        $categories = [
            ['name' => 'Community', 'name_url' => 'community', 'ordering' => 0],
            ['name' => 'Houses', 'name_url' => 'houses', 'ordering' => 1],
            ['name' => 'Jobs', 'name_url' => 'jobs', 'ordering' => 2],
            ['name' => 'Pets', 'name_url' => 'pets', 'ordering' => 3],
            ['name' => 'Rentals', 'name_url' => 'rentals', 'ordering' => 4],
            ['name' => 'Services', 'name_url' => 'services', 'ordering' => 5],
            ['name' => 'Stuff', 'name_url' => 'stuff', 'ordering' => 6],
            ['name' => 'Tickets', 'name_url' => 'tickets', 'ordering' => 7],
            ['name' => 'Vehicles', 'name_url' => 'vehicles', 'ordering' => 8],
        ];

        if ($this->repository->getModel()->newQuery()->exists()) {
            return;
        }
        foreach ($categories as $category) {
            $this->repository->getModel()->create($category);
        }
    }

    private function removeUnusedMenuItems(): void
    {
        $repository = resolve(MenuItemRepositoryInterface::class);

        $repository->deleteMenuItem([
            'menu'       => 'marketplace.sidebarMenu',
            'name'       => 'landing',
            'resolution' => 'mobile',
        ]);

        $repository->deleteMenuItem([
            'menu'       => 'marketplace.sidebarMenu',
            'name'       => 'add',
            'resolution' => 'mobile',
        ]);
    }
}
