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
	Route::get('/users/me', 'UserController@me');

	Route::post('/users/{requester}/relationships', 'UserRelationshipsController@store');
	Route::put('/users/{addressee}/relationships/{requester}/status', 'UserRelationshipsController@update');
	Route::delete('/users/{addressee}/relationships/{requester}/status', 'UserRelationshipsController@destroy');
});
