<?php

namespace MetaFox\Comment\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

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

Route::controller(CommentController::class)
    ->prefix('comment')
    ->group(function () {
        Route::post('hide', 'hide');
        Route::get('related-comment', 'getRelatedComments');
        Route::get('history-edit/{id}', 'getCommentHistories');
        Route::get('preview/{id}', 'previewComment');
        Route::patch('{id}/remove-preview', 'removeLinkPreview');
    });

Route::get('comment-lists', [CommentController::class, 'getUsersComment']);

Route::resource('comment', CommentController::class);
