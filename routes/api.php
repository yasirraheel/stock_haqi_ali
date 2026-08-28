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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes with APP_KEY authentication
Route::group(['prefix' => 'public', 'namespace' => 'API', 'middleware' => 'app.key'], function(){
    Route::match(['get', 'post', 'options'], 'random_videos', 'AndroidApiController@random_videos');
    Route::match(['get', 'post', 'options'], 'random_audios', 'AndroidApiController@random_audios');
    Route::match(['get', 'post', 'options'], 'random_photos', 'AndroidApiController@random_photos');
    Route::match(['get', 'post', 'options'], 'all_content', 'AndroidApiController@all_content');
    Route::match(['get', 'post', 'options'], 'videos_list', 'AndroidApiController@videos_list');
    Route::match(['get', 'post', 'options'], 'audios_list', 'AndroidApiController@audios_list');
    Route::match(['get', 'post', 'options'], 'photos_list', 'AndroidApiController@photos_list');
    Route::match(['get', 'post', 'options'], 'effects_list', 'AndroidApiController@effects_list');
    Route::match(['get', 'options'], 'effects_categories', 'AndroidApiController@effects_categories');
    Route::match(['get', 'options'], 'effect_download', 'AndroidApiController@effect_download');
    Route::match(['get', 'post', 'options'], 'effect_process', 'AndroidApiController@effect_process');
    Route::match(['get', 'post', 'options'], 'effect_progress', 'AndroidApiController@effect_progress');
    Route::get('genres', 'AndroidApiController@get_genres');
});

// Main API routes with APP_KEY authentication
Route::group(['prefix' => 'v1','namespace' => 'API', 'middleware' => 'app.key'], function(){
    Route::get('/', 'AndroidApiController@index');
    Route::post('app_details', 'AndroidApiController@app_details');
    Route::post('login', 'AndroidApiController@postLogin');
    Route::post('signup', 'AndroidApiController@postSignup');
    Route::post('logout', 'AndroidApiController@logout');
    Route::post('forgot_password', 'AndroidApiController@forgot_password');
    Route::post('profile', 'AndroidApiController@profile');
    Route::post('profile_update', 'AndroidApiController@profile_update');
    Route::post('account_delete', 'AndroidApiController@account_delete');
    Route::post('check_user_plan', 'AndroidApiController@check_user_plan');
    Route::post('search', 'AndroidApiController@search');
    Route::post('movies/add_edit_movie', 'AndroidApiController@addnew');
    Route::post('movies/generate_description', 'AndroidApiController@generateDescription');
});

// Public Audio API routes for Reel2Reel & frontend apps (CORS enabled)
Route::options('v1/audio', function() {
    return response('', 200, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, X-API-KEY, Authorization'
    ]);
});
Route::get('v1/audio', 'API\AudioApiController@index')->name('api.v1.audio.index');
Route::get('v1/audio/{id}/stream', 'API\AudioApiController@stream')->name('api.v1.audio.stream');

// Public Film Stock API routes for Reel2Reel & frontend apps (CORS enabled)
Route::options('v1/film-stock', function() {
    return response('', 200, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, X-API-KEY, Authorization'
    ]);
});
Route::get('v1/film-stock', 'API\FilmStockApiController@index')->name('api.v1.film-stock.index');
Route::get('v1/film-stock/{id}/stream', 'API\FilmStockApiController@stream')->name('api.v1.film-stock.stream');
