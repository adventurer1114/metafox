<?php

namespace MetaFox\Group\Http\Controllers\Api;

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

//For admincp
Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::group([
        'prefix'     => 'admincp',
        'middleware' => 'auth.admin',
    ], function () {
        Route::resource('group-category', 'CategoryAdminController');
        Route::patch('group-rule-example/active/{id}', 'ExampleRuleAdminController@active');
        Route::resource('group-rule-example/order', 'ExampleRuleAdminController@order');
        Route::resource('group-rule-example', 'ExampleRuleAdminController')->except(['show']);
    });
});

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::group(['prefix' => 'group'], function () {
        Route::post('avatar/{id}', 'GroupController@updateAvatar');
        Route::post('cover/{id}', 'GroupController@updateCover');
        Route::delete('cover/{id}', 'GroupController@removeCover');
        Route::patch('sponsor/{id}', 'GroupController@sponsor');
        Route::patch('feature/{id}', 'GroupController@feature');
        Route::patch('approve/{id}', 'GroupController@approve');
        Route::patch('pending-mode/{id}', 'GroupController@updatePendingMode');

        Route::get('/moderation-right/{id}', 'GroupController@getModerationRights');
        Route::put('/moderation-right/{id}', 'GroupController@updateModerationRights');

        Route::get('suggestion', 'GroupController@suggestion');
        Route::get('mention', 'GroupController@getGroupForMention');
        Route::get('form', 'GroupController@form');

        Route::get('/category', 'CategoryController@index');
        Route::group(['prefix' => 'privacy'], function () {
            Route::get('/{id}', 'GroupController@getPrivacySettings');
            Route::put('/{id}', 'GroupController@updatePrivacySettings');
            Route::put('change-request/{id}', 'GroupController@cancelRequestChangePrivacy');
        });

        Route::put('confirm-rule', 'GroupController@confirmRule');
        Route::put('confirm-answer-question', 'GroupController@confirmAnswerMembershipQuestion');
    });

    Route::get('/group-invite', 'InviteController@index');
    Route::post('/group-invite', 'InviteController@store');
    Route::put('/group-invite', 'InviteController@update');
    Route::delete('/group-invite', 'InviteController@deleteGroupInvite');
    Route::get('group-info/form/{id}', 'GroupController@infoForm');
    Route::get('group-info/{id}', 'GroupController@groupInfo');
    Route::get('group-about/form/{id}', 'GroupController@aboutForm');
    Route::resource('group', 'GroupController');

    Route::get('group-request', 'RequestController@index');
    Route::put('group-request/accept-request', 'RequestController@acceptMemberRequest');
    Route::delete('group-request/deny-request', 'RequestController@denyMemberRequest');
    Route::delete('group-request/cancel-request/{id}', 'RequestController@cancelRequest');

    Route::resource('group-mute', 'MuteController');
    Route::delete('group-mute', 'MuteController@destroy');

    Route::put('group-member/change-to-moderator', 'MemberController@changeToModerator');
    Route::delete('group-member/remove-group-admin', 'MemberController@removeGroupAdmin');
    Route::delete('group-member/remove-group-moderator', 'MemberController@removeGroupModerator');
    Route::delete('group-member/remove-group-member', 'MemberController@deleteGroupMember');
    Route::post('group-member/add-group-admin', 'MemberController@addGroupAdmins');
    Route::post('group-member/add-group-moderator', 'MemberController@addGroupModerators');
    Route::put('group-member/reassign-owner', 'MemberController@reassignOwner');
    Route::delete('group-member/cancel-invite', 'MemberController@cancelInvitePermission');
    Route::resource('group-member', 'MemberController')->except(['update', 'show']);

    Route::group(['prefix' => 'group-question'], function () {
        Route::get('/form/{id?}', 'QuestionController@form');
        Route::get('/answer-form/{id}', 'QuestionController@answerForm');
        Route::post('/answer', 'QuestionController@createAnswer')->middleware(['array_normalize']);
    });

    Route::resource('group-question', 'QuestionController')->except(['show']);
    Route::put('group-rule/order', 'RuleController@orderRules');
    Route::get('group-rule/form', 'RuleController@createForm');
    Route::get('group-rule/form/{id}', 'RuleController@editForm');
    Route::resource('group-rule', 'RuleController')->except(['show']);
    Route::get('group-rule-example', 'ExampleRuleController@index');

    Route::resource('group-block', 'BlockController');
    Route::delete('group-unblock', 'BlockController@unblock');
    Route::group(['prefix' => 'invite-code'], function () {
        Route::post('/', 'GroupInviteCodeController@store');
        Route::get('/verify/{code}', 'GroupInviteCodeController@verify');
        Route::post('/accept/{code}', 'GroupInviteCodeController@accept');
    });

    Route::get('group-announcement', 'AnnouncementController@index');
    Route::post('group-announcement', 'AnnouncementController@store');
    Route::delete('group-announcement', 'AnnouncementController@removeAnnouncement');
    Route::post('group-announcement/hide', 'AnnouncementController@hide');
});
