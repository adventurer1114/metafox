<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/storage/config/edit/{driver}/{name}', function ($name) {
    return seo_sharing_view(
        'admin.storage.edit_config',
        null,
        null,
        function ($data) use ($name) {
            $data->addBreadcrumb(__p('storage::phrase.configurations'), '/admincp/storage/config/browse');
            $data->addBreadcrumb($name, null);
        }
    );
});
