<?php

namespace MetaFox\Video\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Video\Models\Category;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (Category::query()->exists()) {
            return;
        }

        $categories = [
            ['name' => 'Gaming', 'name_url' => 'gaming', 'ordering' => 1],
            ['name' => 'Film & Entertainment', 'name_url' => 'film-&-entertainment', 'ordering' => 2],
            ['name' => 'Comedy', 'name_url' => 'comedy', 'ordering' => 3],
            ['name' => 'Music', 'name_url' => 'music', 'ordering' => 4],
        ];

        foreach ($categories as $category) {
            Category::query()->Create($category);
        }
    }
}
