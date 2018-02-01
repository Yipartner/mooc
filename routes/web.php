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
Route::get('/user/info/{user_id}','UserController@getUserInfo');
Route::post('/user/edit','UserController@userInfoEdit')->middleware('token');
Route::get('/user/test','UserController@test')->middleware('token');


Route::post('/lesson/create','LessonController@createLesson')->middleware('token');
Route::post('/lesson/update','LessonController@updateLesson')->middleware('token');
Route::get('/lesson/info/{lessonId}','LessonController@getLessonInfo');
Route::get('/lesson/list','LessonController@getLessonList');
Route::get('/lesson/delete/{lessonId}','LessonController@deleteLesson')->middleware('token');