<?php

namespace MetaFox\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Blog\Models\Category;

/**
 * Class PackageSeeder.
 * @codeCoverageIgnore
 * @ignore
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
        if(Category::query()->exists())
            return;

        $categories = [
            [
                'name'     => 'Business',
                'name_url' => 'business',
                'ordering' => 0,
            ],
            [
                'name'     => 'Education',
                'name_url' => 'education',
                'ordering' => 1,
            ],
            [
                'name'     => 'Entertainment',
                'name_url' => 'entertainment',
                'ordering' => 2,
            ],
            [
                'name'     => 'Family & Home',
                'name_url' => 'family-&-home',
                'ordering' => 3,
            ],
            [
                'name'     => 'Health',
                'name_url' => 'health',
                'ordering' => 4,
            ],
            [
                'name'     => 'Recreation',
                'name_url' => 'recreation',
                'ordering' => 5,
            ],
            [
                'name'     => 'Shopping',
                'name_url' => 'shopping',
                'ordering' => 6,
            ],
            [
                'name'     => 'Society',
                'name_url' => 'society',
                'ordering' => 7,
            ],
            [
                'name'     => 'Sports',
                'name_url' => 'sports',
                'ordering' => 8,
            ],
            [
                'name'     => 'Technology',
                'name_url' => 'technology',
                'ordering' => 9,
            ],
        ];

        Category::query()->insert($categories);
    }
}
