<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return seo_sharing_view('activity.feed.home');
});
