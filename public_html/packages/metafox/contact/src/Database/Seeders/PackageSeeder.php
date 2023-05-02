<?php

namespace MetaFox\Contact\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Contact\Models\Category;

/**
 * stub: packages/database/seeder-database.stub.
 */

/**
 * Class PackageSeeder.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->categories();
    }

    private function categories()
    {
        if (Category::query()->exists()) {
            return;
        }

        $categories = [
            [
                'name'     => 'Sales',
                'name_url' => 'sales',
                'ordering' => 0,
            ],
            [
                'name'     => 'Support',
                'name_url' => 'support',
                'ordering' => 1,
            ],
            [
                'name'     => 'Suggestions',
                'name_url' => 'Suggestions',
                'ordering' => 2,
            ],
        ];

        Category::query()->insert($categories);
    }
}
