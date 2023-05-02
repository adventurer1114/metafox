<?php

namespace MetaFox\Photo\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Photo\Models\Category;

/**
 * Class PackageSeeder.
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
        $categories = [
            [
                'name'     => 'Another', 'ordering' => 0,
                'name_url' => 'another',
            ],
            [
                'name'     => 'Artisan Crafts', 'ordering' => 1,
                'name_url' => 'artisan-crafts',
            ],
            [
                'name'     => 'Cartoons & Comics', 'ordering' => 2,
                'name_url' => 'cartoons-&-comics',
            ],
            [
                'name'     => 'Comedy', 'ordering' => 3,
                'name_url' => 'comedy',
            ],
            [
                'name'     => 'Community Projects', 'ordering' => 4,
                'name_url' => 'community-projects',
            ],
            [
                'name'     => 'Contests', 'ordering' => 5,
                'name_url' => 'contests',
            ],
            [
                'name'     => 'Customization', 'ordering' => 6,
                'name_url' => 'customization',
            ],
            [
                'name'     => 'Designs & Interfaces', 'ordering' => 7,
                'name_url' => 'designs-&-interfaces',
            ],
            [
                'name'     => 'Digital Art', 'ordering' => 8,
                'name_url' => 'digital-art',
            ],
            [
                'name'     => 'Fan Art', 'ordering' => 9,
                'name_url' => 'fan-art',
            ],
            [
                'name'     => 'Film & Animation', 'ordering' => 10,
                'name_url' => 'film-&-animation',
            ],
            [
                'name'     => 'Fractal Art', 'ordering' => 11,
                'name_url' => 'fractal-art',
            ],
            [
                'name'     => 'Game Development Art', 'ordering' => 12,
                'name_url' => 'game-development-art',
            ],
            [
                'name'     => 'Literature', 'ordering' => 13,
                'name_url' => 'literature',
            ],
            [
                'name'     => 'People', 'ordering' => 14,
                'name_url' => 'people',
            ],
            [
                'name'     => 'Pets & Animals', 'ordering' => 15,
                'name_url' => 'pets-&-animals',
            ],
            [
                'name'     => 'Photography', 'ordering' => 16,
                'name_url' => 'photography',
            ],
            [
                'name'     => 'Resources & Stock Images', 'ordering' => 17,
                'name_url' => 'resources-&-stock-images',
            ],
            [
                'name'     => 'Science & Technology', 'ordering' => 18,
                'name_url' => 'science-&-technology',
            ],
            [
                'name'     => 'Sports', 'ordering' => 19,
                'name_url' => 'sports',
            ],
            [
                'name'     => 'Traditional Art', 'ordering' => 20,
                'name_url' => 'traditional-art',
            ],
        ];

        if(Category::query()->exists()){
            return;
        }

        Category::query()->insert($categories);
    }
}
