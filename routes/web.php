<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified', 'role:Customer'])
    ->group(function (): void {
        Route::view('customer/dashboard', 'dashboards.customer')->name('customer.dashboard');
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
