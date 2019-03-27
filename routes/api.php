<?php

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
# api登录
Route::post('login', 'ApiController@signin');

Route::group(['middleware' => 'auth:api'], function() {
    $c = 'ApiController@';
    Route::post('student_consumption', $c . 'studentConsumption');
    Route::post('send_msg', $c . 'sendMsg');
});