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
	
	/* app init routes */
    Route::get('init', ['uses' => 'v1\AppInitController@init']);
    Route::post('get/day', ['uses' => 'v1\AppInitController@getDay']);


    /* app user routes */
    Route::post('user/ticket', ['middleware' => 'verified', 'uses' => 'v1\UserController@ticket']);
    Route::post('user/login', ['middleware' => 'auth', 'uses' => 'v1\UserController@login']);
    Route::get('user/logout', ['middleware' => 'loggedin', 'uses' => 'v1\UserController@logout']);
    Route::post('user/registration', ['middleware' => 'auth', 'uses' => 'v1\UserController@registration']);
    Route::post('user/changepassword', ['middleware' => 'auth', 'uses' => 'v1\UserController@passwordReset']);
    Route::post('user/changelanguage', ['middleware' => 'auth', 'uses' => 'v1\UserController@changeLanguage']);


    /* app sofa fan routes */
    Route::get('sofafan/init', ['middleware' => 'verified', 'uses' => 'v1\AppInitController@initSofaFan']);


    /* app non-match day routes */
    Route::get('nonmatchday/init', ['middleware' => 'verified', 'uses' => 'v1\AppInitController@initNonMatchDay']);
    Route::post('nonmatchday/contactus', ['middleware' => 'verified', 'uses' => 'v1\PutDataController@contactus']);


    /* message the team routes */
    Route::get('messagetheteam/get', ['middleware' => 'loggedin', 'uses' => 'v1\PagesController@messageTheTeam']);
    Route::post('messagetheteam/post', ['middleware' => 'loggedin', 'uses' => 'v1\PutDataController@messageTheTeam']);


    /* ask the fans routes */
    Route::get('askthefans/get', ['middleware' => 'loggedin', 'uses' => 'v1\PagesController@askTheFans']);
    Route::post('askthefans/post', ['middleware' => 'loggedin', 'uses' => 'v1\PutDataController@askTheFans']);


    /* predict and win routes */
    Route::get('predictandwin/get', ['middleware' => 'loggedin', 'uses' => 'v1\PagesController@predictAndWin']);
    Route::get('predictandwin/history/get', ['middleware' => 'loggedin', 'uses' => 'v1\PagesController@predictAndWinHistory']);
    Route::post('predictandwin/post', ['middleware' => 'loggedin', 'uses' => 'v1\PutDataController@predictAndWin']);


    /* spotify routes */
    Route::get('spotify/get', ['middleware' => 'loggedin', 'uses' => 'v1\PagesController@spotify']);
    Route::post('spotify/post', ['middleware' => 'loggedin', 'uses' => 'v1\PutDataController@spotify']);


    /* player of the match */
    Route::get('mvp/get', ['middleware' => 'loggedin', 'uses' => 'v1\PagesController@mvp']);
    Route::post('mvp/post', ['middleware' => 'loggedin', 'uses' => 'v1\PutDataController@mvp']);


    /* fans help routes */
    Route::get('fanshelp/get', ['middleware' => 'verified', 'uses' => 'v1\PagesController@fanshelp']);
    Route::post('fanshelp/post', ['middleware' => 'verified', 'uses' => 'v1\PutDataController@fanshelp']);


    /* the mall routes */
    Route::post('mall/get', ['middleware' => 'verified', 'uses' => 'v1\PagesController@mall']);

});
