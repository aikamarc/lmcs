<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//  ROUTES
Route::GET('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::POST('/getSkin', [App\Http\Controllers\HomeController::class, 'getSkin'])->name('getSkin');
Route::POST('/selectSkin', [App\Http\Controllers\HomeController::class, 'selectSkin'])->name('selectSkin');
Route::POST('/updatePb', [App\Http\Controllers\HomeController::class, 'updatePb'])->name('updatePb');

// AUTH
Route::GET('/auth', [App\Http\Controllers\SteamController::class, 'auth'])->name('auth');

// ADMIN
Route::GET('/loadSkin/{token}', [App\Http\Controllers\HomeController::class, 'loadSkin'])->name('loadSkin');
Route::GET('/loadPrice/{token}', [App\Http\Controllers\HomeController::class, 'loadPrice'])->name('loadPrice');
