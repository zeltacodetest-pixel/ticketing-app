<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Admin\DeveloperController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Developer\DashboardController as DeveloperDashboardController;

/*
|--------------------------------------------------------------------------
| 👋 Welcome Page
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome');

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:Customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', CustomerDashboardController::class)->name('dashboard');

        // Create Ticket
        Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

        // View All Tickets
        Route::get('/tickets', [CustomerDashboardController::class, 'showTickets'])->name('tickets.show');

 // View Ticket Messages / Comments
Route::get('/tickets/{ticket}/messages', [CustomerDashboardController::class, 'showMessages'])->name('ticket.messages');
Route::post('/tickets/{ticket}/comment', [CustomerDashboardController::class, 'storeComment'])
    ->name('tickets.comment');

    });


/*
|--------------------------------------------------------------------------
| Developer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:Developer'])
    ->prefix('developer')
    ->name('developer.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DeveloperDashboardController::class, 'index'])->name('dashboard');

        // Ticket Comments
        Route::get('/ticket/{id}/comments', [DeveloperDashboardController::class, 'getComments'])->name('ticket.comments');
        Route::post('/ticket/{id}/comment', [DeveloperDashboardController::class, 'storeComment'])->name('ticket.comment');

        // Ticket Status Update
        Route::post('/ticket/{id}/status', [DeveloperDashboardController::class, 'updateStatus'])->name('ticket.status');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Developer Management
        Route::get('/developers', [DeveloperController::class, 'index'])->name('developers');
        Route::post('/developers', [DeveloperController::class, 'store'])->name('developers.store');
        Route::put('/developers/{developer}', [DeveloperController::class, 'update'])->name('developers.update');
        Route::delete('/developers/{developer}', [DeveloperController::class, 'destroy'])->name('developers.destroy');

        // Ticket Management
        Route::post('/tickets/{id}/assign', [AdminDashboardController::class, 'assignTicket'])->name('assign-ticket');
        Route::delete('/tickets/{id}', [AdminDashboardController::class, 'deleteTicket'])->name('delete-ticket');
    });

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
