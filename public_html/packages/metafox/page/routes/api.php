<?php

namespace MetaFox\Page\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::controller(PageController::class)->prefix('page')->group(function () {
    Route::post('avatar/{id}', 'updateAvatar');
    Route::post('cover/{id}', 'updateCover');
    Route::delete('cover/{id}', 'removeCover');
    Route::patch('sponsor/{id}', 'sponsor');
    Route::patch('feature/{id}', 'feature');
    Route::patch('approve/{id}', 'approve');
    Route::get('mention', 'getPageForMention');
    Route::get('suggestion', 'suggestion');
    Route::get('form/{id?}', 'form');
    Route::get('similar', 'similar');
    Route::get('privacy/{id}', 'getPrivacySettings');
    Route::put('privacy/{id}', 'updatePrivacySettings');
});

Route::controller(PageController::class)->group(function () {
    Route::post('page-claim/{id}', 'claimPage');
    Route::get('page-info/form/{id}', 'infoForm');
    Route::get('page-info/{id}', 'pageInfo');
    Route::get('page-about/form/{id}', 'aboutForm');
});

Route::controller(BlockController::class)->group(function () {
    Route::delete('page-unblock', 'unblock');
});

Route::controller(PageMemberController::class)->group(function () {
    Route::get('page-admin', 'viewPageAdmins');
    Route::post('page-admin', 'addPageAdmins');
    Route::delete('page-admin', 'deletePageAdmin');
    Route::post('page-member/add-page-admin', 'addPageAdmins');
    Route::delete('page-member/remove-page-admin', 'removePageAdmin');
    Route::delete('page-member/remove-page-member', 'deletePageMember');
    Route::put('page-member/reassign-owner', 'reassignOwner');
    Route::delete('page-member/cancel-invite', 'cancelAdminInvite');
});

Route::resource('page/category', PageCategoryController::class)->only(['index']);
Route::resource('page', PageController::class);
Route::resource('page-invite', PageInviteController::class);
Route::resource('page-member', PageMemberController::class)->except(['update', 'show']);
Route::resource('page-block', BlockController::class);
