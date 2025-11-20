<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| These routes are accessible without authentication.
*/

// Landing (home) page
Route::get('/', function () {
    if (auth()->check()) {
        // User is authenticated, redirect to appropriate dashboard
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('clients.index');
        }
    } else {
        // User is not authenticated, show welcome page
        return view('welcome');
    }
})->name('home');

// Authentication routes (login, register, forgot password, etc.)
require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
| Only authenticated users can access these routes.
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard redirect logic (role-based)
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('clients.index');
        }
    })->name('dashboard');

    // Rep routes (reps and admins)
    Route::middleware(['role:rep,admin'])->group(function () {
        Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');
        Route::resource('clients', ClientController::class)->except(['edit', 'update']);
    });

    // Analytics routes (different controllers for different roles)
    Route::middleware(['role:rep'])->group(function () {
        Route::get('/analytics', [ClientController::class, 'analytics'])->name('analytics');
    });

    // Admin routes (admin only)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/clients', [AdminController::class, 'allClients'])->name('clients');
        Route::get('/clients/export', [AdminController::class, 'exportAllClients'])->name('clients.export');
        Route::get('/reps', [AdminController::class, 'reps'])->name('reps');
        Route::get('/rep-performance', [AdminController::class, 'repPerformance'])->name('rep-performance');
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/rep/{rep}/details', [AdminController::class, 'repDetails'])->name('rep-details');
        Route::get('/rep/{rep}/export', [AdminController::class, 'exportRepClients'])->name('rep-export');
    });
});
