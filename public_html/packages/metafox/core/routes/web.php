<?php

namespace MetaFox\Core\Http\Controllers;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(HomeController::class)
    ->group(function () {
        Route::get('/', 'index');
    });

Route::get('sharing', function () {
    return seo_sharing_view('activity.feed.home');
});
