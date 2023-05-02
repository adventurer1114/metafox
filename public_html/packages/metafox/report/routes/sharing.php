<?php

use Illuminate\Support\Facades\Route;
use MetaFox\Platform\Contracts\Content;

Route::get('admincp/report/aggregate/{id}/item/browse', function ($id) {
    return seo_sharing_view(
        'admin.report.browse_report_item',
        'report_item_aggregate',
        $id,
        function ($data, $aggregate) use ($id) {
            $item   = $aggregate?->item;
            $label  = $item instanceof Content ? $item->toTitle() : __p('report::phrase.report_item_aggregate_id', ['id' => $id]);
            $data->addBreadcrumb($label, null);
        }
    );
});
