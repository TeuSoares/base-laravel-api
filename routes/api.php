<?php

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Billing\Controllers\BillingController;
use App\Modules\Billing\Controllers\CheckoutController;
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
    });

Route::middleware(['api', 'auth:web'])->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [AuthController::class, 'me'])->name('me');
    });

    Route::prefix('billing')
        ->name('billing.')
        ->group(function () {
            Route::post('/checkout', [CheckoutController::class, 'initiate'])->name('checkout');
        });
});

Route::middleware(['api', 'auth:web', 'subscribed'])->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::patch('/', [UserController::class, 'update'])->name('update');
    });

    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'subscription'])->name('subscription');
        Route::post('/cancel', [BillingController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [BillingController::class, 'resume'])->name('resume');
    });
});
