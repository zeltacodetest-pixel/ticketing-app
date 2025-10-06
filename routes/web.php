<?php

use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified', 'role:Customer'])
    ->group(function (): void {
        Route::get('customer/dashboard', CustomerDashboardController::class)->name('customer.dashboard');
        Route::post('customer/tickets', [TicketController::class, 'store'])->name('customer.tickets.store');
    });

Route::middleware(['auth', 'verified', 'role:Developer'])
    ->group(function (): void {
        Route::view('developer/dashboard', 'dashboards.developer')->name('developer.dashboard');
    });

Route::middleware(['auth', 'verified', 'role:Admin'])
    ->group(function (): void {
        Route::view('admin/dashboard', 'dashboards.admin')->name('admin.dashboard');
    });

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
