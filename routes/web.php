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

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\UsersController;

// Auth
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class, 'login'])->name('login.attempt')->middleware('guest');
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('register', [RegisterController::class, 'register'])->name('register')->middleware('guest');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/', [DashboardController::class, '__invoke'])->name('dashboard')->middleware('auth');

// Users
Route::get('users', [UsersController::class, 'index'])->name('users')->middleware('remember', 'auth');
Route::get('users/create', [UsersController::class, 'create'])->name('users.create')->middleware('auth');
Route::post('users', [UsersController::class, 'store'])->name('users.store')->middleware('auth');
Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit')->middleware('auth');
Route::put('users/{user}', [UsersController::class, 'update'])->name('users.update')->middleware('auth');
Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy')->middleware('auth');
Route::put('users/{user}/restore', [UsersController::class, 'restore'])->name('users.restore')->middleware('auth');

// Images
Route::get('/img/{path}', [ImagesController::class, 'show'])->where('path', '.*');

// Trainings
Route::get('trainings', [TrainingController::class, 'index'])->name('trainings')->middleware('remember', 'auth');
Route::get('trainings/create', [TrainingController::class, 'create'])->name('trainings.create')->middleware('auth');
Route::get('trainings/{training}/view', [TrainingController::class, 'view'])->name('trainings.view')->middleware('auth');
Route::post('trainings', [TrainingController::class, 'store'])->name('trainings.store')->middleware('auth');
Route::get('trainings/{training}/edit', [TrainingController::class, 'edit'])->name('trainings.edit')->middleware('auth');
Route::put('trainings/{training}', [TrainingController::class, 'update'])->name('trainings.update')->middleware('auth');
Route::delete('trainings/{training}', [TrainingController::class, 'destroy'])->name('trainings.destroy')->middleware('auth');
Route::post('trainings/{training}/attend', [TrainingController::class, 'attend'])->name('trainings.attend')->middleware('auth');
Route::delete('trainings/{training}/withdraw', [TrainingController::class, 'withdraw'])->name('trainings.withdraw')->middleware('auth');

// Photo albums
Route::get('albums', [AlbumController::class, 'index'])->name('albums')->middleware('auth');
Route::get('albums/create', [AlbumController::class, 'create'])->name('albums.create')->middleware('auth');
Route::post('albums', [AlbumController::class, 'store'])->name('albums.store')->middleware('auth');
Route::get('albums/{album}/view', [AlbumController::class, 'view'])->name('albums.view')->middleware('auth');
Route::get('albums/{album}/edit', [AlbumController::class, 'edit'])->name('albums.edit')->middleware('auth');
Route::get('trainings')->name('trainings')->uses('TrainingController@index')->middleware('remember', 'auth');
Route::get('trainings/create')->name('trainings.create')->uses('TrainingController@create')->middleware('auth');
Route::get('trainings/{training}/view')->name('trainings.view')->uses('TrainingController@view')->middleware('auth');
Route::post('trainings')->name('trainings.store')->uses('TrainingController@store')->middleware('auth');
Route::get('trainings/{training}/edit')->name('trainings.edit')->uses('TrainingController@edit')->middleware('auth');
Route::put('trainings/{training}')->name('trainings.update')->uses('TrainingController@update')->middleware('auth');
Route::delete('trainings/{training}')->name('trainings.destroy')->uses('TrainingController@destroy')->middleware('auth');
Route::post('trainings/{training}/attend')->name('trainings.attend')->uses('TrainingController@attend')->middleware('auth');
Route::delete('trainings/{training}/withdraw')->name('trainings.withdraw')->uses('TrainingController@withdraw')->middleware('auth');

// Messages
Route::get('messages')->name('messages')->uses('MessagesController@index')->middleware('remember', 'auth');
Route::get('messages/create')->name('messages.create')->uses('MessagesController@create')->middleware('auth');
Route::post('messages')->name('messages.store')->uses('MessagesController@store')->middleware('auth');
Route::get('messages/{message}/edit')->name('messages.edit')->uses('MessagesController@edit')->middleware('auth');
Route::put('messages/{message}')->name('messages.update')->uses('MessagesController@update')->middleware('auth');
Route::delete('messages/{message}')->name('messages.destroy')->uses('MessagesController@destroy')->middleware('auth');
