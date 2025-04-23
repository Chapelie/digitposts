<?php

use App\Http\Controllers\Authcontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;

Route::get('/', [FeedController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

