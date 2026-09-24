<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobCardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('customers/cards', [CustomerController::class, 'cards'])
            ->name('customers.cards');

        Route::resource('customers', CustomerController::class);
        Route::resource('customers.vehicles', VehicleController::class)
            ->scoped()
            ->except(['index']);

        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('job-cards', [JobCardController::class, 'index'])->name('job-cards.index');
    });
});
