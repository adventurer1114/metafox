<?php

namespace MetaFox\StaticPage\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use MetaFox\Platform\PackageManager;
use MetaFox\StaticPage\Models\StaticPage;
use MetaFox\StaticPage\Repositories\StaticPageRepositoryInterface;

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
        if(StaticPage::query()->exists()){
            return;
        }

        $pages  = resolve(StaticPageRepositoryInterface::class);
        $config = PackageManager::getConfig('metafox/static-page');

        $pages->updateOrCreate([
            'slug' => 'term-of-use',
        ], [
            'slug'            => 'term-of-use',
            'title'           => 'Term of Uses',
            'user_id'         => 1,
            'user_type'       => 'user',
            'owner_id'        => 1,
            'owner_type'      => 'user',
            'module_id'       => 'core',
            'text'            => Arr::get($config, 'pages.term', '[YOUR CONTENT HERE]'),
            'disallow_access' => '',
        ]);

        $pages->updateOrCreate([
            'slug' => 'policy',
        ], [
            'slug'            => 'policy',
            'title'           => 'Privacy',
            'user_id'         => 1,
            'user_type'       => 'user',
            'owner_id'        => 1,
            'owner_type'      => 'user',
            'module_id'       => 'core',
            'text'            => Arr::get($config, 'pages.policy', '[YOUR CONTENT HERE]'),
            'disallow_access' => '',
        ]);
    }
}
