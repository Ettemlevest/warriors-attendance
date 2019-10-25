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
