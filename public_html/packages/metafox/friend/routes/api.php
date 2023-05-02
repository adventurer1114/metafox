<?php

namespace MetaFox\Friend\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    // FriendListController
    Route::post('friend/list/add-friend/{id}', 'FriendListController@addFriendToList');
    Route::delete('friend/list/delete-friend/{id}', 'FriendListController@deleteFriendFromList');
    Route::get('friend/list/form/{id}', 'FriendListController@formUpdate');
    Route::resource('friend/list', 'FriendListController');
    Route::get('friend/list/assign/{id}', 'FriendListController@getAssigned');
    Route::post('friend/list/assign/{id}', 'FriendListController@setAssigned');
    Route::put('friend/list/add-friend/{id}', 'FriendListController@updateToFriendList');

    //  FriendRequestController
    Route::resource('friend/request', 'FriendRequestController');
    Route::post('friend/request/markAllAsRead', 'FriendRequestController@markAllAsRead');

    // FriendController
    Route::get('friend/mention', 'FriendController@mention');
    Route::get('friend/invite-to-owner', 'FriendController@inviteFriendToOwner');
    Route::get('friend/invite-to-item', 'FriendController@inviteFriendsToItem');
    Route::get('friend/tag-suggestion', 'FriendController@tagSuggestion');
    Route::get('friend/suggestion', 'FriendController@suggestion');
    Route::get('friend/birthday', 'FriendController@getFriendBirthdays');
    Route::post('friend/suggestion/hide-user', 'FriendController@hideUserSuggestion');
    Route::resource('friend', 'FriendController');
});
