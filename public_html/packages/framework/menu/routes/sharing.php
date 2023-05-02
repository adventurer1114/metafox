<?php

use Illuminate\Support\Facades\Route;

Route::get('admincp/menu/menu/{id}/menu-item/browse', function ($id) {
    return seo_sharing_view(
        'admin.menu.browse_menu_item',
        'menu',
        $id,
        function ($data, $menu) use ($id) {
            $label  = $menu?->title;
            if (!$label && $menu) {
                $label = $menu?->name;
            }

            if (!$label) {
                $label = 'Menu #' . $id;
            }
            $data->addBreadcrumb($label, null);
        }
    );
});
