<?php

namespace MetaFox\Music\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Music\Models\Genre;

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
        $this->genres();

        $this->createStorageDisk();
    }

    private function createStorageDisk(): void
    {
        app('storage')->tap('music', [
            'label'     => 'Music',
            'is_system' => true,
        ]);
    }

    private function genres()
    {
        $genres = [
            [
                'name'     => 'Alternative',
                'name_url' => 'alternative',
                'ordering' => 0,
            ],
            [
                'name'     => 'Classic Rock',
                'name_url' => 'classic-rock',
                'ordering' => 1,
            ],
            [
                'name'     => 'Country',
                'name_url' => 'country',
                'ordering' => 2,
            ],
            [
                'name'     => 'Electronica',
                'name_url' => 'electronica',
                'ordering' => 3,
            ],
            [
                'name'     => 'Folk',
                'name_url' => 'folk',
                'ordering' => 4,
            ],
            [
                'name'     => 'Hardcore',
                'name_url' => 'hardcore',
                'ordering' => 5,
            ],
            [
                'name'     => 'Hip hop',
                'name_url' => 'hip-hop',
                'ordering' => 6,
            ],
            [
                'name'     => 'House',
                'name_url' => 'house',
                'ordering' => 7,
            ],
            [
                'name'     => 'Indie',
                'name_url' => 'indie',
                'ordering' => 8,
            ],
            [
                'name'     => 'Jazz',
                'name_url' => 'jazz',
                'ordering' => 9,
            ],
        ];

        $musicGenres = Genre::query()->exists();
        if (!$musicGenres) {
            Genre::query()->insert($genres);
        }
    }
}
