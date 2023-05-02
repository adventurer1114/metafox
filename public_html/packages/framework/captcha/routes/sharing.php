<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/captcha/type/edit/{name}', function ($name) {
    return seo_sharing_view(
        'admin.captcha.edit_type',
        null,
        null,
        function ($data) use ($name) {
            $data->addBreadcrumb(__p('captcha::admin.captcha_types'), '/admincp/captcha/type/browse');
            $data->addBreadcrumb($name, null);
        }
    );
});
