<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/upload/token','FileUploadController@getUploadToken');
Route::post('/upload/callback','FileUploadController@callback');
Route::post('/notify','FileUploadController@notify');

Route::post('/user/register','UserController@register');
Route::post('/user/login','UserController@login');
Route::get('/user/test','UserController@test')->middleware('token');