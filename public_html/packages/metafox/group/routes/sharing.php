<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/group/category/{id}/category/browse', function ($id) {
    return seo_sharing_view(
        'admin.group.browse_child_category',
        'group_category',
        $id,
        function ($data, $resource) use ($id) {
            $label  = $resource?->title;
            if (!$label) {
                $label = 'Category #' . $id;
            }
            $data->addBreadcrumb(__p('core::phrase.categories'), '/admincp/group/category/browse');
            $data->addBreadcrumb($label, null);
        }
    );
});
