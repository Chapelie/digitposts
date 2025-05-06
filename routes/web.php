<?php

use App\Http\Controllers\Authcontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\CreatorController;

Route::get('/', [FeedController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'loginform'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registerform'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [Creatorcontroller::class,'index'])->name('dashboard');
    Route::get('/dashboard/campaigns/new', [Creatorcontroller::class,'campaignCreate'])->name('campaigns.create');
    Route::post('/dashboard/create', [Creatorcontroller::class,'store']);
    Route::get('/dashboard/campaigns', [Creatorcontroller::class,'campaignIndex'])->name('campaigns.index');
    Route::post('/dashboard/campaigns/store', [Creatorcontroller::class,'campaignStore'])->name('campaigns.store');
    route::post('/registration',[FeedController::class,'registration'])->name('registration');
});

Route::get('/dashboard/campaigns/show/{id}', [FeedController::class,'show'])->name('campaigns.show');
