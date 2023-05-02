<?php

use Illuminate\Support\Facades\Route;
use MetaFox\Profile\Models\Field;
use MetaFox\Profile\Models\Section;

Route::get('admincp/profile/field/edit/{id}', function ($id) {
    return seo_sharing_view(
        'admin.profile.edit_field',
        'user_custom_field',
        $id,
        function ($data, $resource) use ($id) {
            if (!$resource) {
                $resource = Field::query()->find($id);
            }

            $data->addBreadcrumb('Manage Custom Fields', '/admincp/profile/field/browse');
            $data->addBreadcrumb($resource->label, null);
        }
    );
});

Route::get('admincp/profile/section/edit/{id}', function ($id) {
    return seo_sharing_view(
        'admin.profile.edit_section',
        'user_custom_section',
        $id,
        function ($data, $resource) use ($id) {
            if (!$resource) {
                $resource = Section::query()->find($id);
            }

            $data->addBreadcrumb('Manage Custom Group', '/admincp/profile/section/browse');
            $data->addBreadcrumb($resource->label, null);
        }
    );
});
