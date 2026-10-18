<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/settings/profile', [AuthorController::class, 'edit'])->name('profile.edit');
    Route::put('/settings/profile', [AuthorController::class, 'update'])->name('profile.update');
    Route::put('/settings/password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->name('password.update');
    Route::get('/liked', [LikeController::class, 'index'])->name('likes.index');

    Route::get('/posts/new', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->middleware('throttle:20,1')->name('comments.store');
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->middleware('throttle:60,1')->name('posts.like');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tags.show');
Route::get('/@{user}', [AuthorController::class, 'show'])->name('authors.show');
