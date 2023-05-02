<?php

namespace MetaFox\Marketplace\Http\Controllers\Api;

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

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    //routes for marketplace
    Route::controller(ListingController::class)
        ->prefix('marketplace')
        ->group(function () {
            Route::patch('feature/{id}', 'feature');
            Route::patch('sponsor/{id}', 'sponsor');
            Route::put('approve/{id}', 'approve');
            Route::patch('reopen/{id}', 'reopen');
            Route::patch('sponsor-in-feed/{id}', 'sponsorInFeed');
        });

    //routes for marketplace category
    Route::resource('marketplace-category', 'CategoryController');

    //routes for marketplace invite
    Route::controller(InviteController::class)
        ->prefix('marketplace-invite')
        ->group(function () {
            Route::post('marketplace-invite', 'invite');
        });

    Route::controller(InvoiceController::class)
        ->prefix('marketplace-invoice')
        ->group(function () {
            Route::post('change', 'change');
            Route::put('repayment/{id}', 'repayment');
        });

    Route::controller(InviteController::class)
        ->prefix('marketplace-invite')
        ->group(function () {
            Route::get('invited-people', 'getInvitedPeople');
        });

    Route::resource('marketplace-invite', 'InviteController');

    //routes for marketplace photo
    Route::get('marketplace-photo/form/{id}', 'ImageController@form');

    Route::put('marketplace-photo/{id}', 'ImageController@update');

    Route::resource('marketplace', 'ListingController');

    Route::resource('marketplace-invoice', 'InvoiceController');
});
