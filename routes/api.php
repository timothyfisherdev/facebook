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

Route::prefix('api/rest/v1')->namespace('API\REST\v1')->middleware('auth:api')->group(function () {
	Route::get('/users/me', 'UsersController@me');
	Route::get('/users/{user}', 'UsersController@show');

	Route::post('/users/{requester}/relationships', 'UsersRelationshipsController@store');
	Route::put('/users/{addressee}/relationships/{requester}/status', 'UsersRelationshipsController@update');
	Route::delete('/users/{addressee}/relationships/{requester}/status', 'UsersRelationshipsController@destroy');

	Route::get('/posts', 'PostsController@index');
	Route::post('/posts', 'PostsController@store');
});
