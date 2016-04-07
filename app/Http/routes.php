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

Route::get('/', 'HomeController@index');

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    //Route::get('/home', 'HomeController@index');
	Route::get('/browse', 'BrowseController@index');
	Route::get('/create', 'CreateController@index');
	Route::post('/create', 'CreateController@create');
	Route::get('/update', 'UpdateController@index');
	Route::get('/user/{id}', 'UserViewController@index');
	Route::get('/acket/{id}', 'AcketViewController@index');
	Route::post('/acket', 'AcketViewController@UpdateAcket');
	Route::get('/settings', 'SettingsController@index');
	Route::post('/settings', 'SettingsController@updateSettings');
	
});
