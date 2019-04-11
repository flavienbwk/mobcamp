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

    // Notifications routes
    Route::post('account/notifications', 'AccountController@notifications');
    Route::post('account/notification/seen', 'AccountController@notificationSeen');

    // Cooperatives routes
    Route::post('cooperatives', 'CooperativeController@cooperatives');
    Route::post('cooperative', 'CooperativeController@cooperative');
    Route::post('account/cooperatives', 'CooperativeController@userCooperatives');

    // Formations (base)
    Route::post('formations', 'FormationController@formations');
    Route::post('formation', 'FormationController@formation');

    // Formations (follow)
    Route::post('formations/follow', 'FormationController@follow');
    Route::post('formations/unfollow', 'FormationController@unfollow');
    Route::post('formations/isFollowed', 'FormationController@isFollowed');

    // Formations (chapter)
    Route::post('formations/chapter', 'FormationController@chapter');

    // Roles routes
    Route::post('cooperative/roles', 'CooperativeController@roles');
    Route::post('roles', 'CooperativeController@rolesList');
});

Route::group(['middleware' => ['web', 'authenticated', "role_enseignant"]], function() {
    Route::post('formations/add', 'FormationController@add');
    Route::post('formations/remove', 'FormationController@remove');
});

Route::group(['middleware' => ['web', 'authenticated', "role_administrator"]], function() {

    // Cooperative user roles
    Route::post('cooperative/user/add', 'CooperativeController@addUser');
    Route::post('cooperative/user/remove', 'CooperativeController@removeUser');
    Route::post('cooperative/roles/add', 'CooperativeController@addRoles');
    Route::post('cooperative/roles/remove', 'CooperativeController@removeRoles');
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
