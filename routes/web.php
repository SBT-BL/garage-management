<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobCardController;
use App\Http\Controllers\Select2Controller;
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

        Route::get('services/cards', [ServiceController::class, 'cards'])
            ->name('services.cards');

        Route::resource('services', ServiceController::class);

        Route::get('select/customers', [Select2Controller::class, 'customers'])->name('select.customers');
        Route::get('select/vehicles', [Select2Controller::class, 'vehicles'])->name('select.vehicles');
        Route::get('select/services', [Select2Controller::class, 'services'])->name('select.services');

        Route::get('job-cards/cards', [JobCardController::class, 'cards'])
            ->name('job-cards.cards');
        Route::patch('job-cards/{job_card}/status', [JobCardController::class, 'updateStatus'])
            ->name('job-cards.status.update');
        Route::resource('job-cards', JobCardController::class);
    });
});
