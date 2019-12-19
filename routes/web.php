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
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\UsersController;

// Auth
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class, 'login'])->name('login.attempt')->middleware('guest');
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('register', [RegisterController::class, 'register'])->name('register')->middleware(['guest', 'lowercase']);
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
Route::post('trainings/{training}/attendee/{attendee}/confirm', [TrainingController::class, 'confirmAttendance'])->name('trainings.attendance.confirm')->middleware('auth');
Route::post('trainings/{training}/attendee/{attendee}/reject', [TrainingController::class, 'rejectAttendance'])->name('trainings.attendance.reject')->middleware('auth');

// Photo albums
Route::get('albums', [AlbumController::class, 'index'])->name('albums')->middleware('auth');
Route::get('albums/create', [AlbumController::class, 'create'])->name('albums.create')->middleware('auth');
Route::post('albums', [AlbumController::class, 'store'])->name('albums.store')->middleware('auth');
Route::get('albums/{album}/view', [AlbumController::class, 'view'])->name('albums.view')->middleware('auth');
Route::get('albums/{album}/edit', [AlbumController::class, 'edit'])->name('albums.edit')->middleware('auth');
Route::put('albums/{album}', [AlbumController::class, 'update'])->name('albums.update')->middleware('auth');
Route::delete('albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy')->middleware('auth');
Route::post('album/{album}/cover/{photo}', [AlbumController::class, 'setCover'])->name('albums.cover')->middleware('auth');
Route::delete('photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy')->middleware('auth');

// Messages
Route::get('messages', [MessagesController::class, 'index'])->name('messages')->middleware('remember', 'auth');
Route::get('messages/create', [MessagesController::class, 'create'])->name('messages.create')->middleware('auth');
Route::post('messages', [MessagesController::class, 'store'])->name('messages.store')->middleware('auth');
Route::get('messages/{message}/edit', [MessagesController::class, 'edit'])->name('messages.edit')->middleware('auth');
Route::put('messages/{message}', [MessagesController::class, 'update'])->name('messages.update')->middleware('auth');
Route::delete('messages/{message}', [MessagesController::class, 'destroy'])->name('messages.destroy')->middleware('auth');
