<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/contact/category/{id}/category/browse', function ($id) {
    return seo_sharing_view(
        'admin.contact.browse_child_category',
        'contact_category',
        $id,
        function ($data, $resource) use ($id) {
            $label = $resource?->title;
            if (!$label) {
                $label = 'Category #' . $id;
            }
            $data->addBreadcrumb(__p('core::phrase.categories'), '/admincp/contact/category/browse');
            $data->addBreadcrumb($label, null);
        }
    );
});
