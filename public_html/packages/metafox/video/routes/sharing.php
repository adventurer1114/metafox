<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/video/category/{id}/category/browse', function ($id) {
    return seo_sharing_view(
        'admin.video.browse_child_category',
        'video_category',
        $id,
        function ($data, $resource) use ($id) {
            $label = $resource?->title;
            if (!$label) {
                $label = 'Category #' . $id;
            }
            $data->addBreadcrumb(__p('core::phrase.categories'), '/admincp/video/category/browse');
            $data->addBreadcrumb($label, null);
        }
    );
});
