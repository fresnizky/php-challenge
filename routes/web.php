<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/weather/{id}', 'WeatherController@index');
Route::get('/weather/map/{lat}/{lon}', 'WeatherController@getMap');
Route::post('/weather', 'WeatherController@getWeather');
Route::post('/weather/favorite', 'WeatherController@saveFavorite');
Route::post('/weather/deleteFavorite', 'WeatherController@deleteFavorite');