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

// Public API routes with API key authentication
Route::group(['prefix' => 'public', 'namespace' => 'API'], function(){
    Route::post('random_videos', 'AndroidApiController@random_videos');
    Route::post('random_audios', 'AndroidApiController@random_audios');
    Route::post('random_photos', 'AndroidApiController@random_photos');
    Route::post('all_content', 'AndroidApiController@all_content');
    Route::post('videos_list', 'AndroidApiController@videos_list');
    Route::post('audios_list', 'AndroidApiController@audios_list');
    Route::post('photos_list', 'AndroidApiController@photos_list');
});

Route::group(['prefix' => 'v1','namespace' => 'API'], function(){
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
    Route::get('genres', 'AndroidApiController@get_genres');

});
