<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('errors.503');
});

Route::group(['prefix' => 'api', 'middleware' => ['api']], function () {
	
	/* app init routes */
    Route::get('/v1/init/{client}', ['uses' => 'v1\AppInitController@init']);

    /* app user routes */
    // Route::get('/v1/user/login/{client}', ['uses' => 'v1\UserController@login']);
    // Route::get('/v1/user/logout/{client}', ['uses' => 'v1\UserController@login']);
    // Route::get('/v1/user/register/{client}', ['uses' => 'v1\UserController@register']);
    // Route::get('/v1/user/forgotpassword/{client}', ['uses' => 'v1\UserController@forgotPassword']);
});
