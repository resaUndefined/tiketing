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
    Route::post('logout', 'apk\JWTAuthController@logout');
    Route::post('refresh', 'apk\JWTAuthController@refresh');
    Route::get('profile', 'apk\JWTAuthController@profile');
});

Route::group(['middleware' => 'jwt.auth'], function () {
    // Route::get('logout', 'ApiController@logout');
    Route::prefix('roles')->group(function () {
        Route::get('', 'apk\RoleController@get_all_role');
        Route::post('', 'apk\RoleController@add_role');
        Route::get('{id}', 'apk\RoleController@get_role');
        Route::put('{id}', 'apk\RoleController@update_role');
        Route::delete('{id}', 'apk\RoleController@delete_role');
    });
    // Route::get('products', 'ProductController@index');
    // Route::get('products/{id}', 'ProductController@show');
    // Route::post('products', 'ProductController@store');
    // Route::put('products/{id}', 'ProductController@update');
    // Route::delete('products/{id}', 'ProductController@destroy');
});

