<?php

namespace MetaFox\Event\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Event\Models\Category;

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

        if(Category::query()->exists()){
            return;
        }

        $id = 0;

        $categories = [
            [
                'name'     => 'Arts',
                'name_url' => 'arts',
                'ordering' => ++$id,
            ],
            [
                'name'     => 'Comedy',
                'name_url' => 'comedy',
                'ordering' => ++$id,
            ],
            [
                'name'     => 'Movies',
                'name_url' => 'movies',
                'ordering' => ++$id,
            ],
            [
                'name'     => 'Music',
                'name_url' => 'music',
                'ordering' => ++$id,
            ],
            [
                'name'     => 'Other',
                'name_url' => 'other',
                'ordering' => ++$id,
            ],
            [
                'name'     => 'Party',
                'name_url' => 'party',
                'ordering' => ++$id,
            ],
            [
                'name'     => 'Sports',
                'name_url' => 'sports',
                'ordering' => ++$id,
            ],
            [
                'name'     => 'TV',
                'name_url' => 'tv',
                'ordering' => ++$id,
            ],
        ];
        Category::query()->insert($categories);
    }
}
