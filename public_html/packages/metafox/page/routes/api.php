<?php

namespace MetaFox\Page\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::group(['prefix' => 'page'], function () {
        Route::post('avatar/{id}', 'PageController@updateAvatar');
        Route::post('cover/{id}', 'PageController@updateCover');
        Route::delete('cover/{id}', 'PageController@removeCover');
        Route::patch('sponsor/{id}', 'PageController@sponsor');
        Route::patch('feature/{id}', 'PageController@feature');
        Route::patch('approve/{id}', 'PageController@approve');
        Route::get('mention', 'PageController@getPageForMention');
        Route::get('suggestion', 'PageController@suggestion');
        Route::get('form/{id?}', 'PageController@form');
        Route::get('similar', 'PageController@similar');
        Route::resource('category', 'PageCategoryController');

        Route::group(['prefix' => 'privacy'], function () {
            Route::get('/{id}', 'PageController@getPrivacySettings');
            Route::put('/{id}', 'PageController@updatePrivacySettings');
        });
    });

    Route::get('/page-invite', 'PageInviteController@index');
    Route::post('/page-invite', 'PageInviteController@store');
    Route::put('/page-invite', 'PageInviteController@update');
    Route::delete('/page-invite', 'PageInviteController@destroy');

    Route::get('page-claim/form/{id}', 'PageController@claimForm');
    Route::post('page-claim/{id}', 'PageController@claimPage');

    Route::get('page-info/form/{id}', 'PageController@infoForm');
    Route::get('page-info/{id}', 'PageController@pageInfo');

    Route::get('page-about/form/{id}', 'PageController@aboutForm');
    Route::resource('page', 'PageController');

    Route::post('page-member/add-page-admin', 'PageMemberController@addPageAdmins');
    Route::delete('page-member/remove-page-admin', 'PageMemberController@removePageAdmin');
    Route::delete('page-member/remove-page-member', 'PageMemberController@deletePageMember');
    Route::put('page-member/reassign-owner', 'PageMemberController@reassignOwner');
    Route::delete('page-member/cancel-invite', 'PageMemberController@cancelAdminInvite');
    Route::resource('page-member', 'PageMemberController')->except(['update', 'show']);

    Route::resource('page-block', 'BlockController');
    Route::delete('page-unblock', 'BlockController@unblock');

    Route::group(['prefix' => 'page-admin'], function () {
        Route::get('', 'PageMemberController@viewPageAdmins');
        Route::post('', 'PageMemberController@addPageAdmins');
        Route::delete('', 'PageMemberController@deletePageAdmin');
    });
});
