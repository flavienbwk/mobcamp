<?php

use Illuminate\Http\Request;

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

Route::get('/', function () {
    $ApiResponse = new \App\ApiResponse();
    $ApiResponse->setMessage("Reachable API.");
    return Response::json($ApiResponse->getResponse(), 200);
});

Route::group(['middleware' => ['web']], function() {

    // Authentication routes
    Route::post('auth/login', 'AuthController@login');
    Route::post('auth/register', 'AuthController@register');
    Route::post('auth/expiration', 'AuthController@expiration');
});

Route::group(['middleware' => ['web', 'authenticated']], function() {

    // Authentication routes
    Route::post('auth/info', 'AuthController@info');
    Route::post('auth/expiration', 'AuthController@expiration');

    // Avatar-related routes
    Route::post('account/avatar/add', 'AccountController@addAvatar');
    Route::post('account/avatar', 'AccountController@avatar');

    // Notifications route
    Route::post('account/notifications', 'AccountController@notifications');
    Route::post('account/notification/seen', 'AccountController@notificationSeen');

    // User subscriptions
    Route::post('account/subscriptions', 'AccountController@subscriptionsList');
    Route::post('account/subscribed', 'AccountController@subscribedList');
    Route::post('account/subscription', 'AccountController@subscription');
    Route::post('account/issubscribed', 'AccountController@issubscribed');
    Route::post('account/search', 'AccountController@searchUsername');

    // Publications (base)
    Route::post('publications/add', 'PublicationController@add');
    Route::post('publications/remove', 'PublicationController@remove');
    Route::post('publications', 'PublicationController@publications');
    Route::post('publication', 'PublicationController@publication');
    
    // Publications (comments)
    Route::post('publication/comments', 'PublicationController@comments');
    Route::post('publication/comment', 'PublicationController@comment');

    // Publications (reactions)
    Route::post('publication/reactions', 'PublicationController@reactions');
    Route::post('reaction', 'ReactionController@reaction');
    Route::post('reactions', 'ReactionController@reactions');
    
    // Conversations
    Route::post('conversations', 'ConversationController@conversations');
    Route::post('conversations/add', 'ConversationController@add');
    Route::post('conversations/add_user', 'ConversationController@addUser');
    Route::post('conversation/messages', 'ConversationController@messages');
    Route::post('conversation/message', 'ConversationController@message');
    Route::post('conversation/users', 'ConversationController@conversationUsers');
    
    // - Populate the database with images, users and publications
    // - Seeders & migrate tests
    // - Remove docker compose init.d sql file
    // - Commit on GitLab

});

Route::get('{any?}', function ($any = null) {
    $ApiResponse = new \App\ApiResponse();
    $ApiResponse->setMessage("Route not found.");
    return Response::json($ApiResponse->getResponse(), 404);
})->where('any', '.*');

Route::post('{any?}', function ($any = null) {
    $ApiResponse = new \App\ApiResponse();
    $ApiResponse->setMessage("Route not found.");
    return Response::json($ApiResponse->getResponse(), 404);
})->where('any', '.*');