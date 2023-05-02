<?php

namespace MetaFox\User\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;
use MetaFox\Platform\Middleware\PreventPendingSubscription;

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

Route::group(['namespace' => '\MetaFox\User\Http\Controllers'], function () {
    Route::post('register', 'AuthenticateController@register');
    Route::get('test-user', 'AuthenticateController@testUser');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::get('logout', 'AuthenticateController@logout');
            Route::get('profile', 'AuthenticateController@profile');
        });
    });
});

Route::group([
    'namespace' => __NAMESPACE__,
], function () {
    Route::post('user', 'UserController@store');
    Route::post('user/login', 'UserController@login');
    Route::post('user/refresh', 'UserController@refresh');
    Route::post('admincp/login', 'UserAdminController@login');
});

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::group(['prefix' => 'user'], function () {
        Route::get('/form', 'UserController@userForm');

        // api: /account
        Route::group(['prefix' => 'account'], function () {
            Route::get('/', 'UserController@account');
            Route::get('/name-form/', 'AccountController@editNameForm');
            Route::get('/password-form/', 'AccountController@editPasswordForm');
            Route::get('/email-form/', 'AccountController@editEmailForm');
            Route::get('/currency-form/', 'AccountController@editCurrencyForm');
            Route::get('/language-form/', 'AccountController@editLanguageForm');
            Route::get('/username-form/', 'AccountController@editUsernameForm');
            Route::get('/timezone-form/', 'AccountController@editTimezoneForm');
            Route::get('/review-form/', 'AccountController@editReviewTagPostForm');
        });

        // api: /activity
        Route::get('/activity', 'UserController@activity');

        // api: /profile
        Route::get('/profile/form', 'UserController@profileForm');
        Route::get('/profile/gender', 'UserController@genderSuggestion');
        Route::put('/profile/{id?}', 'UserController@updateProfile');

        // api: /user
        Route::get('/info/{user}', 'UserController@infoForm');
        Route::get('/simple/{user}', 'UserController@simple');
        Route::post('/avatar/{user}', 'UserController@uploadAvatar');
        Route::post('/cover/{user}', 'UserController@updateCover');
        Route::put('/remove-cover/{id?}', 'UserController@removeCover');
        Route::get('/quick-preview/{id}', 'UserController@quickPreview');
        Route::patch('/feature/{id}', 'UserController@feature');
        Route::get('/city', 'UserController@citySuggestion');
        Route::get('/country/state', 'UserController@countryStateSuggestion');

        Route::group(['prefix' => 'ban'], function () {
            // Admin ban/un-ban user.
            Route::post('/', 'UserController@banUser');
            Route::delete('/{id}', 'UserController@removeBanUser');
        });

        Route::group(['prefix' => 'shortcut'], function () {
            Route::get('/', 'UserShortcutController@index');
            Route::get('/edit', 'UserShortcutController@viewForEdit');
            Route::put('/manage/{id}', 'UserShortcutController@manage');
        });

        Route::get('{id}/item-stats', 'UserController@getItemStats');
    });

    Route::get('/me', 'UserController@getMe');

    // User CRUD.
    Route::resource('user', 'UserController')->except('store');

    Route::prefix('account')
        ->group(function () {
            Route::get('timezone', 'AccountController@getTimeZones')
                ->name('timezone.index');

            // Block user.
            Route::group(['prefix' => 'blocked-user'], function () {
                Route::get('/', 'AccountController@findAllBlockedUser');
                Route::post('/', 'AccountController@addBlockedUser');
                Route::delete('/{id}', 'AccountController@deleteBlockedUser');
            });

            // User profile.
            Route::group(['prefix' => 'profile-privacy'], function () {
                Route::get('/{id?}', 'AccountController@getProfileSettings');
                Route::put('/', 'AccountController@updateProfileSettings');
            });

            // User profile menu.
            Route::group(['prefix' => 'profile-menu'], function () {
                Route::get('/{id?}', 'AccountController@getProfileMenuSettings');
                Route::put('/', 'AccountController@updateProfileMenuSettings');
            });

            // User item privacy.
            Route::group(['prefix' => 'item-privacy'], function () {
                Route::get('/{id?}', 'AccountController@getItemPrivacySettings');
                Route::put('/', 'AccountController@updateItemPrivacySettings');
            });

            // Account setting
            Route::group(['prefix' => 'setting'], function () {
                Route::get('/', 'AccountController@setting');
                Route::put('/', 'AccountController@updateAccountSetting');
            });

            // Invisible setting
            Route::group(['prefix' => 'invisible'], function () {
                Route::get('/', 'AccountController@getInvisibleSettings');
                Route::put('/', 'AccountController@updateInvisibleSettings');
            });

            Route::group(['prefix' => 'notification'], function () {
                Route::get('/', 'AccountController@getNotificationSettings');
                Route::put('/', 'AccountController@updateNotificationSettings');
            });
        });

//    Route::group(['prefix' => 'admincp'], function () {
//        // manage members
//        Route::get('user', 'UserAdminController@index');
//        Route::get('user/{id}', 'UserAdminController@show');
//        Route::get('user/form/edit/{id}', 'UserAdminController@formUpdate');
//        Route::get('user/form/statistics/{id}', 'UserAdminController@getStatistics');
//        Route::patch('user/approve/{id}', 'UserAdminController@approve');
//
//        // manage cancelled options
//        Route::get('user/cancel/reason', 'CancelReasonAdminController@index');
//        Route::post('user/cancel/reason', 'CancelReasonAdminController@store');
//        Route::get('user/cancel/reason/active/{id}', 'CancelReasonAdminController@active');
//        Route::get('user/cancel/reason/form/{id}', 'CancelReasonAdminController@editForm');
//        Route::get('user/cancel/reason/form', 'CancelReasonAdminController@createForm');
//
//        // manage cancelled options
//        Route::get('user/cancel/feedback', 'CancelFeedbackAdminController@index');
//
//        // promotions
//        Route::get('user/promotion/form', 'UserPromotionAdminController@createForm');
//        Route::get('user/promotion/form/{id}', 'UserPromotionAdminController@editForm');
//        Route::resource('user/promotion', 'UserPromotionAdminController');
//
//        // relationship status
//        Route::get('user/relation/form', 'UserRelationAdminController@createForm');
//        Route::get('user/relation/form/:id', 'UserRelationAdminController@editForm');
//        Route::resource('user/relation', 'UserRelationAdminController');
//    });
});

Route::prefix('user')
    ->as('user.')
    ->group(function () {
        Route::prefix('verify')
            ->controller(UserVerifyController::class)
            ->group(function () {
                Route::post('email/{hash}', 'verify')->name('verify');
                Route::post('resend', 'resend')->name('resend');
            });

        Route::prefix('account')
            ->controller(AccountController::class)
            ->group(function () {
                Route::post('/cancellation', 'cancel')->name('account.cancel')
                    ->withoutMiddleware([PreventPendingSubscription::class]);
            });

        Route::prefix('password')
            ->controller(UserPasswordController::class)
            ->group(function () {
                Route::post('request-method/{resolution}', 'requestMethod')->name('password.request.method');
                Route::post('request-verify/{resolution}', 'requestVerify')->name('password.request.verify');
                Route::post('edit/{resolution}', 'edit')->name('password.edit');
                Route::patch('{resolution?}', 'reset')->name('password.reset');
            });
    });
