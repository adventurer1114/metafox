<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/page/category/{id}/category/browse', function ($id) {
    return seo_sharing_view(
        'admin.page.browse_child_category',
        'page_category',
        $id,
        function ($data, $resource) use ($id) {
            $label  = $resource?->title;
            if (!$label) {
                $label = 'Category #' . $id;
            }
            $data->addBreadcrumb(__p('core::phrase.categories'), '/admincp/page/category/browse');
            $data->addBreadcrumb($label, null);
        }
    );
});
