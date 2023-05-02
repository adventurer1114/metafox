<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/log/file/{file}/msg/browse', function ($file) {
    return seo_sharing_view(
        'admin.log.browse_file_log',
        null,
        null,
        function ($data) use ($file) {
            $data->addBreadcrumb(__p('log::phrase.files'), '/admincp/log/file/browse');
            $data->addBreadcrumb(base64_decode($file), null);
        }
    );
});
Route::get('admincp/log/channel/edit/{driver}/{name}', function ($driver, $name) {
    return seo_sharing_view(
        'admin.log.edit_channel',
        null,
        null,
        function ($data) use ($driver, $name) {
            $data->addBreadcrumb(__p('log::phrase.channels'), '/admincp/log/channel/browse');
            $data->addBreadcrumb("{$name}", null);
        }
    );
});
