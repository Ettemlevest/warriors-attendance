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

// Auth
Route::get('login')->name('login')->uses('Auth\LoginController@showLoginForm')->middleware('guest');
Route::post('login')->name('login.attempt')->uses('Auth\LoginController@login')->middleware('guest');
Route::post('logout')->name('logout')->uses('Auth\LoginController@logout');

// Dashboard
Route::get('/')->name('dashboard')->uses('DashboardController')->middleware('auth');

// Users
Route::get('users')->name('users')->uses('UsersController@index')->middleware('remember', 'auth');
Route::get('users/create')->name('users.create')->uses('UsersController@create')->middleware('auth');
Route::post('users')->name('users.store')->uses('UsersController@store')->middleware('auth');
Route::get('users/{user}/edit')->name('users.edit')->uses('UsersController@edit')->middleware('auth');
Route::put('users/{user}')->name('users.update')->uses('UsersController@update')->middleware('auth');
Route::delete('users/{user}')->name('users.destroy')->uses('UsersController@destroy')->middleware('auth');
Route::put('users/{user}/restore')->name('users.restore')->uses('UsersController@restore')->middleware('auth');

// Images
Route::get('/img/{path}', 'ImagesController@show')->where('path', '.*');

// Reports
Route::get('reports')->name('reports')->uses('ReportsController')->middleware('auth');

// Trainings
Route::get('trainings')->name('trainings')->uses('TrainingController@index')->middleware('auth');
Route::get('trainings/create')->name('trainings.create')->uses('TrainingController@create')->middleware('auth');
Route::post('trainings')->name('trainings.store')->uses('TrainingController@store')->middleware('auth');
Route::get('trainings/{training}/edit')->name('trainings.edit')->uses('TrainingController@edit')->middleware('auth');
Route::put('trainings/{training}')->name('trainings.update')->uses('TrainingController@update')->middleware('auth');
Route::delete('trainings/{training}')->name('trainings.destroy')->uses('TrainingController@destroy')->middleware('auth');
Route::put('trainings/{training}/restore')->name('trainings.restore')->uses('TrainingController@restore')->middleware('auth');
