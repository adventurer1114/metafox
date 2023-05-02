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

Route::group(['prefix' => 'group'], function () {
    Route::controller(GroupController::class)->group(function () {
        Route::post('avatar/{id}', 'updateAvatar');
        Route::post('cover/{id}', 'updateCover');
        Route::delete('cover/{id}', 'removeCover');
        Route::patch('sponsor/{id}', 'sponsor');
        Route::patch('feature/{id}', 'feature');
        Route::patch('approve/{id}', 'approve');
        Route::patch('pending-mode/{id}', 'updatePendingMode');
    });

    Route::controller(GroupController::class)->group(function () {
        Route::get('/moderation-right/{id}', 'getModerationRights');
        Route::put('/moderation-right/{id}', 'updateModerationRights');
        Route::put('confirm-rule', 'confirmRule');
        Route::get('suggestion', 'suggestion');
        Route::get('mention', 'getGroupForMention');
        Route::get('form', 'form');
        Route::put('confirm-answer-question', 'confirmAnswerMembershipQuestion');
    });

    Route::controller(GroupController::class)
        ->prefix('privacy')
        ->group(function () {
            Route::get('/{id}', 'getPrivacySettings');
            Route::put('/{id}', 'updatePrivacySettings');
            Route::put('change-request/{id}', 'cancelRequestChangePrivacy');
        });
});

Route::controller(InviteController::class)->group(function () {
    Route::get('/group-invite', 'index');
    Route::post('/group-invite', 'store');
    Route::put('/group-invite', 'update');
    Route::delete('/group-invite', 'deleteGroupInvite');
});

Route::controller(GroupController::class)->group(function () {
    Route::get('group-info/form/{id}', 'infoForm');
    Route::get('group-info/{id}', 'groupInfo');
    Route::get('group-about/form/{id}', 'aboutForm');
});

Route::controller(RequestController::class)->group(function () {
    Route::get('group-request', 'index');
    Route::put('group-request/accept-request', 'acceptMemberRequest');
    Route::delete('group-request/deny-request', 'denyMemberRequest');
    Route::delete('group-request/cancel-request/{id}', 'cancelRequest');
});

Route::controller(MemberController::class)->prefix('group-member')->group(function () {
    Route::put('change-to-moderator', 'changeToModerator');
    Route::delete('remove-group-admin', 'removeGroupAdmin');
    Route::delete('remove-group-moderator', 'removeGroupModerator');
    Route::delete('remove-group-member', 'deleteGroupMember');
    Route::post('add-group-admin', 'addGroupAdmins');
    Route::post('add-group-moderator', 'addGroupModerators');
    Route::put('reassign-owner', 'reassignOwner');
    Route::delete('cancel-invite', 'cancelInvitePermission');
});

Route::prefix('group-question')
    ->controller(QuestionController::class)
    ->group(function () {
        Route::get('/form/{id?}', 'form');
        Route::get('/answer-form/{id}', 'answerForm');
        Route::post('/answer', 'createAnswer')->middleware(['array_normalize']);
    });

Route::controller(RuleController::class)->group(function () {
    Route::put('group-rule/order', 'orderRules');
    Route::get('group-rule/form', 'createForm');
    Route::get('group-rule/form/{id}', 'editForm');
});

Route::controller(BlockController::class)->group(function () {
    Route::delete('group-unblock', 'unblock');
});

Route::prefix('invite-code')->controller(GroupInviteCodeController::class)->group(function () {
    Route::post('/', 'store');
    Route::get('/verify/{code}', 'verify');
    Route::post('/accept/{code}', 'accept');
});

Route::controller(AnnouncementController::class)->group(function () {
    Route::get('group-announcement', 'index');
    Route::post('group-announcement', 'store');
    Route::delete('group-announcement', 'removeAnnouncement');
    Route::post('group-announcement/hide', 'hide');
});

Route::controller(MuteController::class)->group(function () {
    Route::delete('group-mute', 'destroy');
});

Route::as('group.')->group(function () {
    Route::resource('group/category', CategoryController::class)->only('index');
    Route::resource('group', GroupController::class)->names('.');
    Route::resource('group-mute', MuteController::class);
    Route::resource('group-member', MemberController::class)->except(['update', 'show']);
    Route::resource('group-question', QuestionController::class)->except(['show']);
    Route::resource('group-rule', RuleController::class)->except(['show']);
    Route::resource('group-rule-example', ExampleRuleController::class)->only('index');
    Route::resource('group-block', BlockController::class);
});
