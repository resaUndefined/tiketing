<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api'

], function ($router) {
    Route::post('register', 'apk\JWTAuthController@register');
    Route::post('login', 'apk\JWTAuthController@login');
    Route::post('refresh', 'apk\JWTAuthController@refresh');
    Route::get('profile', 'apk\JWTAuthController@profile');
});

Route::get('logout', 'apk\JWTAuthController@logout')->middleware(['api', 'jwt.auth']);

Route::group(['middleware' => ['api', 'jwt.auth.custom']], function () {
    // Route::get('logout', 'ApiController@logout');

    // roles
    Route::prefix('roles')->group(function () {
        Route::get('', 'apk\RoleController@get_all_role');
        Route::post('', 'apk\RoleController@add_role');
        Route::get('{id}', 'apk\RoleController@get_role');
        Route::put('{id}', 'apk\RoleController@update_role');
        Route::delete('{id}', 'apk\RoleController@delete_role');
    });

    // users
    Route::prefix('users')->group(function () {
        Route::get('', 'apk\UserController@get_all_user');
        Route::post('', 'apk\UserController@add_user');
        Route::get('{id}', 'apk\UserController@get_user');
        Route::put('{id}', 'apk\UserController@update_user');
        Route::delete('{id}', 'apk\UserController@delete_user');
        Route::put('change-password/{id}', 'apk\UserController@change_password');
        Route::post('change-status/{id}', 'apk\UserController@change_status');
    });
});

