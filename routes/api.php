<?php

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->name('auth.')
    ->middleware(['api'])
    ->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

        Route::middleware('auth:web')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('me', [AuthController::class, 'me'])->name('me');
        });
    });

Route::prefix('user')
    ->name('user.')
    ->middleware(['api', 'auth:web'])
    ->group(function () {
        Route::patch('/', [UserController::class, 'update'])->name('update');
    });
