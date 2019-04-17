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
    Route::post('formations/followed', 'FormationController@formationsFollowed');
    Route::post('formations/search', 'FormationController@formationsByName');

    // Formations (follow)
    Route::post('formations/follow', 'FormationController@follow');
    Route::post('formations/unfollow', 'FormationController@unfollow');
    Route::post('formations/isFollowed', 'FormationController@isFollowed');

    // Certificates
    Route::post('certificates', 'CertificateController@certificates');

    // Roles routes
    Route::post('cooperative/roles', 'CooperativeController@roles');
    Route::post('roles', 'CooperativeController@rolesList');

    // Tours
    Route::post('cooperative/tours', 'TourController@list');

    // Cooperative items
    Route::post('cooperative/items', 'ItemController@list');
    Route::post('cooperative/item', 'ItemController@details');

    // Cooperative inventory
    Route::post('cooperative/inventory', 'CooperativeController@inventory');

    // User inventory in cooperative
    Route::post('account/inventory', 'AccountController@inventory');
    Route::post('account/inventory/add', 'AccountController@inventoryAdd');
    Route::post('account/inventory/remove', 'AccountController@inventoryRemove');

    // Orders
    Route::post('account/orders', 'OrderController@listUser');
    Route::post('cooperative/buy', 'OrderController@sell');
    Route::post('cooperative/sell', 'OrderController@sell');

});

Route::group(['middleware' => ['web', 'authenticated', "role_enseignant"]], function() {
    // formations actions
    Route::post('formations/add', 'FormationController@add');
    Route::post('formations/remove', 'FormationController@remove');

    // chapters actions
    Route::post('chapters/addLesson', 'ChapterController@addLesson');
    Route::post('chapters/addActivity', 'ChapterController@addActivity');
    Route::post('chapters/addQuizz', 'ChapterController@addQuizz');
    Route::post('chapters/uploadMedia', 'ChapterController@uploadMedia');
    Route::post('chapters/removeMedia', 'ChapterController@removeMedia');
});

Route::group(['middleware' => ['web', 'authenticated', "role_commercial"]], function() {
    
    // Tours
    Route::post('cooperative/tours/add', 'TourController@add');
    Route::post('cooperative/tours/remove', 'TourController@remove');

    // Schedules for tours
    Route::post('cooperative/tour/schedules', 'TourController@listSchedules');
    Route::post('cooperative/tour/schedules/add', 'TourController@addSchedule');
    Route::post('cooperative/tour/schedules/remove', 'TourController@removeSchedule');

    // Cooperative items
    Route::post('cooperative/items/add', 'ItemController@add');
    Route::post('cooperative/items/remove', 'ItemController@remove');
    Route::post('cooperative/item/add_image', 'ItemController@addImage');
    Route::post('cooperative/item/remove_image', 'ItemController@removeImage');

    // Cooperative inventory
    Route::post('cooperative/inventory/add', 'CooperativeController@inventoryAdd');
    Route::post('cooperative/inventory/remove', 'CooperativeController@inventoryRemove');
    Route::post('cooperative/users/items', 'CooperativeController@inventoryUsers');

    // Orders
    Route::post('cooperative/orders', 'OrderController@list');
    Route::post('cooperative/order/items', 'OrderController@listItems');
    Route::post('cooperative/order/approve', 'OrderController@approve');
    Route::post('cooperative/order/desapprove', 'OrderController@desapprove');
});

Route::group(['middleware' => ['web', 'authenticated', "role_administrator"]], function() {
    // Cooperative user roles
    Route::post('cooperative/users/add', 'CooperativeController@addUser');
    Route::post('cooperative/users/remove', 'CooperativeController@removeUser');
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
