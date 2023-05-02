<?php

use Illuminate\Support\Facades\Route;

Route::get('user/{id}', function ($id) {
    return seo_sharing_view('user.user.landing', 'user', $id);
});

Route::get('user', function () {
    return seo_sharing_view('user.user.landing');
});

Route::get('admincp/user/user/edit/{id}', function ($id) {
    return seo_sharing_view(
        'admin.user.edit_user',
        'user',
        $id,
        function ($data, $user) {
            $data->addBreadcrumb('Manage Members', '/admincp/user/user/browse');
            $data->addBreadcrumb($user?->user_name, null);
        }
    );
});
