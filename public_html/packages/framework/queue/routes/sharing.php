<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/queue/connection/edit/{driver}/{name}', function ($driver, $name) {
    return seo_sharing_view(
        'admin.queue.edit_connection',
        null,
        null,
        function ($data) use ($driver, $name) {
            $data->addBreadcrumb(__p('queue::phrase.connections'), '/admincp/queue/connection/browse');
            $data->addBreadcrumb(ucfirst($name), null);
        }
    );
});
