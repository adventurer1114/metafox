<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/event/category/{id}/category/browse', function ($id) {
    return seo_sharing_view(
        'admin.event.browse_child_category',
        'event_category',
        $id,
        function ($data, $resource) use ($id) {
            $label = $resource?->title;
            if (!$label) {
                $label = 'Category #' . $id;
            }
            $data->addBreadcrumb(__p('core::phrase.categories'), '/admincp/event/category/browse');
            $data->addBreadcrumb($label, null);
        }
    );
});
