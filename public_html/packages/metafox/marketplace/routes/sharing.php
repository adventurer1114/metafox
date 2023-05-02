<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/marketplace/category/{id}/category/browse', function ($id) {
    return seo_sharing_view(
        'admin.marketplace.browse_child_category',
        'marketplace_category',
        $id,
        function ($data, $resource) use ($id) {
            $label = $resource?->title;
            if (!$label) {
                $label = 'Category #' . $id;
            }
            $data->addBreadcrumb(__p('core::phrase.categories'), '/admincp/marketplace/category/browse');
            $data->addBreadcrumb($label, null);
        }
    );
});
