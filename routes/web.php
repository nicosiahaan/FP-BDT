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

//home
Route::get('/','HomeController@show_home');
Route::get('/login','HomeController@show_login');
Route::get('/dashboard','HomeController@show_dashboard');

//user
Route::post('/login','UserController@login')->name('login');
Route::get('/logout','UserController@logout')->name('logout');
Route::post('/register/user','UserController@register_user')->name('register_user');

//examination
Route::post('/register/examination','ExaminationController@create_examination')->name('create_examination');
Route::get('/examination/detail/{examination_id}','ExaminationController@show_examination_detail')->name('show_examination_detail');

//question
Route::post('/register/question','ExaminationController@create_question')->name('create_question');
Route::post('/register/question/image','ExaminationController@create_image')->name('create_image');

//student
Route::post('/assign/user','ExaminationController@assign_student_examination')->name('assign_student_examination');

//exam
Route::get('/exam/{examination_id}','ExaminationController@do_exam')->name('do_exam');
Route::post('/exam/question/answer','ExaminationController@answer')->name('answer');
Route::get('/exam/scoring/{examination_id}','ExaminationController@show_examination_score')->name('scoring');

//scoring
Route::post('/exam/scoring/answer','ExaminationController@scoring')->name('scoringanswer');

//clarification
Route::post('exam/ask/clarification','ExaminationController@ask_clarification')->name('ask_clarification');
Route::post('exam/answer/clarification','HomeController@answer_clarification')->name('answer_clarification');

//test redis
Route::get('/test','ExaminationController@test_redis');