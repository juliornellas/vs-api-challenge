<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', RegisterController::class)->name('register');
Route::post('login', LoginController::class)->name('login');
Route::get('posts', [PostController::class, 'index'])->name('posts.index');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('session', SessionController::class)->name('session');
    Route::post('logout', LogoutController::class)->name('logout');
    Route::apiResource('posts', PostController::class, ['except' => ['index']]);
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('posts/{post}/favorite', [FavoriteController::class, 'storeOrDestroyFavoritePost'])->name('favorites.posts');
    Route::post('users/{user}/favorite', [FavoriteController::class, 'storeOrDestroyFavoriteUser'])->name('favorites.users');
    Route::get('users/import/{start}/{limit}', [UserController::class, 'import'])->name('users.import');
});