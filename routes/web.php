<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) return redirect()->route('dashboard');
    return view('welcome');
})->name('home');

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD REDIRECT ---
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) return redirect()->route('admin.analytics');
        if ($user->hasRole('commercial') || $user->hasRole('rep')) return redirect()->route('clients.index');
        // Hatim and Logistics staff go here:
        if ($user->hasRole('logistics') || $user->hasRole('stock_manager')) return redirect()->route('tools.logistics.index');
        return view('welcome');
    })->name('dashboard');

    // --- 1. CRM / CLIENTS ---
    Route::middleware(['role:admin,commercial,rep'])->group(function () {
        Route::resource('clients', ClientController::class);
        Route::get('/analytics', [ClientController::class, 'analytics'])->name('analytics');
        Route::get('/calendar', [ClientController::class, 'calendar'])->name('calendar');
    });

    // --- 2. ADMIN PANEL ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/analytics/data', [AdminController::class, 'getChartData'])->name('analytics.data');
        Route::get('/clients', [AdminController::class, 'allClients'])->name('clients');
    });

    // --- 3. TOOLS HUB ---
    Route::prefix('tools')->name('tools.')->group(function () {
        
        // General Tools
        Route::get('/', function () { return view('tools.index'); })->name('index');
        
        // --- THIS WAS MISSING AND CAUSED THE ERROR ---
        Route::get('/label-generator', function () { return view('tools.label-generator'); })->name('label');

        // ====================================================
        // LOGISTICS MODULE - SMART SYSTEM
        // ====================================================

        // A. AJAX APIS (Smart Inputs & Anti-Double Entry)
        Route::get('/api/check-bl', [LogisticsController::class, 'checkBlExists'])->name('api.check.bl');
        Route::get('/api/catalog-search', [LogisticsController::class, 'searchCatalog'])->name('api.catalog.search');

        // B. END OF DAY RITUAL (GSAP Animation)
        Route::middleware(['role:admin,logistics,stock_manager'])->group(function() {
            Route::get('/logistics/closing', [LogisticsController::class, 'dailyClosing'])->name('logistics.closing');
        });

        // C. CORE LOGISTICS
        Route::middleware(['role:admin,logistics,stock_manager'])->group(function() {
            // NEW: Product Flow History
            Route::get('/logistics/registry', [LogisticsController::class, 'articleIndex'])->name('logistics.articles.index');
            Route::get('/logistics/registry/details', [LogisticsController::class, 'articleShow'])->name('logistics.articles.show');
            Route::post('/logistics/registry/update', [LogisticsController::class, 'updateCatalogProduct'])->name('logistics.articles.update_global');
            
            Route::get('/logistics', [LogisticsController::class, 'dashboard'])->name('logistics.index');
            Route::get('/logistics/archives', [LogisticsController::class, 'archives'])->name('logistics.archives');
            Route::get('/news', [LogisticsController::class, 'news'])->name('news.index');
            
            // Creation (Specific routes FIRST)
            Route::get('/logistics/create', [LogisticsController::class, 'create'])->name('logistics.create');
            Route::post('/logistics/store', [LogisticsController::class, 'store'])->name('logistics.store');
            // Updates & Actions
            Route::get('/logistics/{bl}/edit', [LogisticsController::class, 'edit'])->name('logistics.edit'); // NEW
            Route::delete('/logistics/{bl}', [LogisticsController::class, 'destroy'])->name('logistics.destroy');
            Route::put('/logistics/{bl}', [LogisticsController::class, 'update'])->name('logistics.update');   // NEW
            
            // Updates & Actions
            Route::put('/logistics/{bl}/status', [LogisticsController::class, 'updateStatus'])->name('logistics.updateStatus');
            Route::post('/logistics/{bl}/note', [LogisticsController::class, 'addNote'])->name('logistics.note');
            // Article Management
            Route::get('/logistics/article/{article}/edit', [LogisticsController::class, 'editArticleDetails'])->name('logistics.article.edit_details');
            Route::put('/logistics/article/{article}/details', [LogisticsController::class, 'updateArticleDetails'])->name('logistics.article.update_details');
            Route::put('/logistics/article/{article}', [LogisticsController::class, 'updateArticle'])->name('logistics.article.update');
            Route::delete('/logistics/article/{article}', [LogisticsController::class, 'destroyArticle'])->name('logistics.article.delete');
        });

        // D. WILDCARD SHOW (Must be LAST to not conflict with 'create' or 'closing')
        Route::middleware(['role:admin,logistics,stock_manager'])->group(function() {
            Route::get('/logistics/{bl}', [LogisticsController::class, 'show'])->name('logistics.show');
        });

        // SAV & INCIDENTS
        Route::middleware(['role:admin,stock_manager'])->group(function() {
            Route::get('/sav', [LogisticsController::class, 'incidents'])->name('sav.index');
            Route::post('/sav/store', [LogisticsController::class, 'storeIncident'])->name('sav.store');
        });

        // NEWS ADMIN
        Route::middleware(['role:admin'])->group(function() {
            Route::get('/news/create', [LogisticsController::class, 'createNews'])->name('news.create');
            Route::post('/news/store', [LogisticsController::class, 'storeNews'])->name('news.store');
        });
    });

        // Global Routes (Sidebar Links)
    Route::get('/catalog', function () { return view('products'); })->name('products.catalog');
    Route::get('/documentation', function () { return view('documentation'); })->name('documentation');

    // Global
    Route::get('/catalog', function () { return view('products'); })->name('products.catalog');
});