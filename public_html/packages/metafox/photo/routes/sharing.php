<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/photo/category/{id}/category/browse', function ($id) {
    return seo_sharing_view(
        'admin.photo.browse_child_category',
        'photo_category',
        $id,
        function ($data, $resource) use ($id) {
            $label  = $resource?->title;
            if (!$label) {
                $label = 'Category #' . $id;
            }
            $data->addBreadcrumb(__p('core::phrase.categories'), '/admincp/photo/category/browse');
            $data->addBreadcrumb($label, null);
        }
    );
});
