<?php

namespace MetaFox\User\Database\Seeders;

use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;

/**
 * Class PackageSeeder.
 * @codeCoverageIgnore
 * @ignore
 */
class PackageSeeder extends Seeder
{
    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AuthPassportTableSeeder::class);
        $this->call(AuthRoleTableSeeder::class);
        $this->call(UserGenderTableSeeder::class);
    }
}
