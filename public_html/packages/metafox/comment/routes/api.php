<?php

namespace MetaFox\Comment\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;
use MetaFox\Comment\Http\Controllers\Api\v1\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::controller(CommentController::class)
        ->prefix('comment')
        ->group(function () {
            Route::post('hide', 'hide');
            Route::get('related-comment', 'getRelatedComments');
            Route::get('history-edit/{id}', 'getCommentHistories');
            Route::get('preview/{id}', 'previewComment');
        });

    Route::get('comment-lists', [CommentController::class, 'getUsersComment']);

    Route::resource('comment', 'CommentController');
});
