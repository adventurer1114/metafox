<?php

namespace MetaFox\BackgroundStatus\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use MetaFox\BackgroundStatus\Models\BgsBackground;
use MetaFox\BackgroundStatus\Models\BgsCollection;
use MetaFox\Platform\PackageManager;

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
        $filename = base_path('packages/metafox/background-status/resources/collections.json');

        $collections = json_decode(file_get_contents($filename), true);

        foreach ($collections as $value) {
            $exists = BgsCollection::query()->where([
                ['title', '=', $value['title']],
            ])->count();

            if ($exists) {
                continue;
            }

            /** @var BgsCollection $collection */
            $collection = BgsCollection::query()->updateOrCreate(['title' => $value['title']], [
                'title'            => $value['title'],
                'is_default'       => Arr::get($value, 'is_default', 1),
                'view_only'        => Arr::get($value, 'view_only', 1),
                'total_background' => count($value['items']),
            ]);

            $this->addBackgrounds($collection, $value['items']);
        }
    }

    /**
     * @param BgsCollection       $collection
     * @param array<string,mixed> $items
     */
    private function addBackgrounds(BgsCollection $collection, array $items): void
    {
        $chunks = [];

        $directory = PackageManager::getAssetPath('metafox/background-status');

        $storage = app('storage');
        foreach ($items as $index => $item) {
            $localPath =  $item['path'];
            $filename  = $directory . '/' . $localPath;

            $file = $storage->putFileAs('asset', 'assets/bgs', $filename, $localPath);

            $chunks[] = [
                'collection_id' => $collection->id,
                'image_path'    => $item['path'],
                'server_id'     => $file->path,
                'image_file_id' => $file->id,
                'view_only'     => 1,
                'ordering'      => Arr::get($item, 'ordering', $index),
            ];
        }

        BgsBackground::query()->insert($chunks);

        /** @var BgsBackground $thumbBackground */
        $thumbBackground = $collection->backgrounds()->first();
        $collection->update([
            'main_background_id' => $thumbBackground->entityId(),
        ]);
    }
}
