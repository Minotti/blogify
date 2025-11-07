<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PublicPostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public routes
Route::get('/blog', [PublicPostController::class, 'index'])->name('public.posts.index');
Route::get('/blog/{post}', [PublicPostController::class, 'show'])->name('public.posts.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('posts', PostController::class);
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/{source}', [ImportController::class, 'import'])->name('import.store');
});
