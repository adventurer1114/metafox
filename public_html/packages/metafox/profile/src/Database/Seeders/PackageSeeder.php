<?php

namespace MetaFox\Profile\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Profile\Models\Field;
use MetaFox\Profile\Models\Profile;
use MetaFox\Profile\Models\Section;

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
        if(Profile::query()->exists()){
            return;
        }

        Profile::query()->upsert([
            ['profile_type' => 'user', 'user_type' => 'user'],
        ], ['profile_type']);

        Section::query()->upsert([
            [
                'name'      => 'about',
                'is_active' => 1,
            ],
        ], ['name']);

        Field::query()->upsert([
            [
                'field_name'  => 'about_me',
                'type_id'     => 'string',
                'section_id'  => 1,
                'edit_type'   => 'textArea',
                'view_type'   => 'text',
                'is_required' => false,
                'is_active'   => true,
                'ordering'    => 1,
            ],
            [
                'field_name'  => 'bio',
                'type_id'     => 'string',
                'section_id'  => 1,
                'edit_type'   => 'textArea',
                'view_type'   => 'text',
                'is_required' => false,
                'is_active'   => true,
                'ordering'    => 2,
            ],
            [
                'field_name'  => 'interest',
                'type_id'     => 'string',
                'section_id'  => 1,
                'edit_type'   => 'textArea',
                'view_type'   => 'text',
                'is_required' => false,
                'is_active'   => true,
                'ordering'    => 3,
            ],
            [
                'field_name'  => 'hobbies',
                'type_id'     => 'string',
                'section_id'  => 1,
                'edit_type'   => 'textArea',
                'view_type'   => 'text',
                'is_required' => false,
                'is_active'   => true,
                'ordering'    => 4,
            ],
        ], ['field_name']);
    }
}
