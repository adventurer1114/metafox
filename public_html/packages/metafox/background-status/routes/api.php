<?php

namespace MetaFox\BackgroundStatus\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::resource('pstatusbg-collection', 'BgsCollectionController');
    Route::get('bgs-collection-admin', 'BgsCollectionController@viewBgsCollectionsForAdmin');
    Route::get('bgs-background', 'BgsCollectionController@getBackgrounds');
    Route::delete('bgs-background/{id}', 'BgsCollectionController@deleteBackground');
});
