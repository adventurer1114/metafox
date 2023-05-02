<?php

namespace MetaFox\Announcement\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Announcement\Models\Style;

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
        $this->styles();
    }

    /**
     * @todo update image path later
     */
    private function styles()
    {
        if(Style::query()->exists()){ // skip type seeder after installed.
            return;
        }

        $icons = [
            'success' => [
                'image' => 'announcement-ico_success.png',
                'font'  => 'ico-check-circle-alt',
            ],
            'info' => [
                'image' => 'announcement-ico_info.png',
                'font'  => 'ico-newspaper-o',
            ],
            'warning' => [
                'image' => 'announcement-ico_warning.png',
                'font'  => 'ico-warning-o',
            ],
            'danger' => [
                'image' => 'announcement-ico_danger.png',
                'font'  => 'ico-fire',
            ],
        ];

        foreach ($icons as $name => $icon) {
            $params = [
                'name'       => $name,
                'icon_image' => $icon['image'],
                'icon_font'  => $icon['font'],
            ];
            Style::firstOrCreate($params);
        }
    }
}
