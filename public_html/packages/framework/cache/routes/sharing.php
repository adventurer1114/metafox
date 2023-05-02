<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/cache/store/edit/{driver}/{name}', function ($driver, $name) {
    return seo_sharing_view(
        'admin.cache.edit_store',
        null,
        null,
        function ($data) use ($driver, $name) {
            $data->addBreadcrumb(__p('cache::phrase.cache_storages'), '/admincp/cache/store/browse');
            $data->addBreadcrumb(ucfirst($name), null);
        }
    );
});
