<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/blog/category/{id}/category/browse', function ($id) {
    return seo_sharing_view(
        'admin.blog.browse_child_category',
        'blog_category',
        $id,
        function ($data, $resource) use ($id) {
            $label  = $resource?->title;
            if (!$label) {
                $label = 'Category #' . $id;
            }
            $data->addBreadcrumb(__p('core::phrase.categories'), '/admincp/blog/category/browse');
            $data->addBreadcrumb($label, null);
        }
    );
});
