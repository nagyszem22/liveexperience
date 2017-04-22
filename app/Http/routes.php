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

Route::group(['prefix' => 'api/v1', 'middleware' => ['api']], function () {

    ///////////////////////////////////////////////////////////
    // @todo - create a new route+service group called pages
    // to refresh app pages
    ///////////////////////////////////////////////////////////
	
	/* app init routes */
    Route::get('init/{client}', ['uses' => 'v1\AppInitController@init']);
    Route::get('get/day/{client}', ['uses' => 'v1\AppInitController@getDay']);

    /* app user routes */
    Route::post('user/ticket/{client}', ['uses' => 'v1\UserController@ticket']);
    Route::post('user/login/{client}', ['uses' => 'v1\UserController@login']);
    Route::post('user/registration/{client}', ['uses' => 'v1\UserController@registration']);
    // Route::get('/v1/user/logout/{client}', ['uses' => 'v1\UserController@login']);
    // Route::get('/v1/user/forgotpassword/{client}', ['uses' => 'v1\UserController@forgotPassword']);

    /* app sofa fan routes */
    Route::get('sofafan/init/{client}/{languageId}', ['uses' => 'v1\AppInitController@initSofaFan']);

    /* app non-match day routes */
    Route::get('nonmatchday/init/{client}/{languageId}', ['uses' => 'v1\AppInitController@initNonMatchDay']);
    // Route::get('nonmatchday/lineup/{client}/{languageId}/{teamId}', ['uses' => '...']);
    // Route::get('nonmatchday/match/{client}/{languageId}/{teamId}', ['uses' => '...']);
    Route::post('nonmatchday/contactus/{client}', ['uses' => 'v1\PutDataController@contactus']);
});
