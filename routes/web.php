<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SalesCockpitController; // <--- MAKE SURE THIS IS IMPORTED
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIC ROUTES ---
Route::get('/', function () {
    if (auth()->check()) return redirect()->route('dashboard');
    return view('welcome');
})->name('home');

// --- 2. AUTHENTICATION ---
// (Included at the bottom)

// --- 3. PROTECTED APP ROUTES ---
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD DISPATCHER ---
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.analytics');
        }
        
        if ($user->hasRole('commercial') || $user->hasRole('rep')) {
            return redirect()->route('clients.index');
        }
        
        if ($user->hasRole('logistics') || $user->hasRole('stock_manager')) {
            return redirect()->route('tools.logistics.index');
        }

        return view('welcome');
    })->name('dashboard');


    // ====================================================
    // A. ADMIN PANEL (Prefix: /admin)
    // ====================================================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Analytics
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/analytics/data', [AdminController::class, 'getChartData'])->name('analytics.data');

        // Exports
        Route::get('/clients/export/all', [AdminController::class, 'exportAllClients'])->name('clients.export'); 
        Route::get('/clients/export/{rep}', [AdminController::class, 'exportRepClients'])->name('clients.export.rep');

        // Client Management
        Route::get('/clients', [AdminController::class, 'allClients'])->name('clients');

        // Reps Management
        Route::get('/reps', [AdminController::class, 'reps'])->name('reps');
        Route::get('/reps/{user}', [AdminController::class, 'repDetails'])->name('reps.show');
        
        // News Admin
        Route::get('/news/create', [LogisticsController::class, 'createNews'])->name('news.create');
        Route::post('/news/store', [LogisticsController::class, 'storeNews'])->name('news.store');
    });


    // ====================================================
    // B. CRM / SALES (Commercial & Reps)
    // ====================================================
    Route::middleware(['role:admin,commercial,rep'])->group(function () {
        
        // Analytics & Calendar
        Route::get('/crm/analytics', [ClientController::class, 'analytics'])->name('analytics');
        Route::get('/crm/calendar', [ClientController::class, 'calendar'])->name('calendar');

        // Export (Before resource)
        Route::get('clients/export', [ClientController::class, 'export'])->name('clients.export');

        // Client Resource
        Route::resource('clients', ClientController::class);
    });


    // ====================================================
    // C. TOOLS & LOGISTICS HUB (Prefix: /tools)
    // ====================================================
    Route::prefix('tools')->name('tools.')->group(function () {
        
        // 1. General Tools
        Route::get('/', function () { return view('tools.index'); })->name('index');
        Route::get('/label-generator', function () { return view('tools.label-generator'); })->name('label');
        
        // --- NEW: AI ROOM VISUALIZER ---
        Route::get('/visualizer', [App\Http\Controllers\VisualizationController::class, 'index'])->name('visualizer.index');
        Route::post('/visualizer/generate', [App\Http\Controllers\VisualizationController::class, 'visualize'])->name('visualizer.generate');

        // --- NEW: SALES COCKPIT ---
        Route::middleware(['role:admin,commercial,rep'])->group(function() {
            Route::get('/sales-cockpit', [SalesCockpitController::class, 'index'])->name('sales.index');
            Route::post('/sales-cockpit/store', [SalesCockpitController::class, 'store'])->name('sales.store');
            Route::get('/sales-cockpit/{client}', [SalesCockpitController::class, 'show'])->name('sales.show');
            Route::get('/sales-cockpit/{client}/edit', [SalesCockpitController::class, 'edit'])->name('sales.edit');
            Route::put('/sales-cockpit/{client}', [SalesCockpitController::class, 'update'])->name('sales.update');
            Route::post('/sales-cockpit/{client}/note', [SalesCockpitController::class, 'updateNote'])->name('sales.note');
            Route::get('/sales-cockpit-performance', [SalesCockpitController::class, 'performance'])->name('sales.performance');
            Route::get('/sales-cockpit-agenda', [SalesCockpitController::class, 'agenda'])->name('sales.agenda');
            Route::post('/sales-cockpit-agenda/store', [SalesCockpitController::class, 'storeTask'])->name('sales.tasks.store');
            Route::post('/sales-cockpit-agenda/reorder', [SalesCockpitController::class, 'reorderTasks'])->name('sales.tasks.reorder');
            Route::post('/sales-cockpit-agenda/{task}/toggle', [SalesCockpitController::class, 'toggleTask'])->name('sales.tasks.toggle');
            Route::delete('/sales-cockpit-agenda/{task}', [SalesCockpitController::class, 'destroyTask'])->name('sales.tasks.destroy');
            Route::get('/sales-cockpit-news', [SalesCockpitController::class, 'news'])->name('sales.news');
        });

        // 2. Logistics API
        Route::get('/api/check-bl', [LogisticsController::class, 'checkBlExists'])->name('api.check.bl');
        Route::get('/api/catalog-search', [LogisticsController::class, 'searchCatalog'])->name('api.catalog.search');

        // 3. Logistics Operations
        Route::middleware(['role:admin,logistics,stock_manager'])->group(function() {
            
            // --- Specific Actions ---
            Route::get('/logistics/closing', [LogisticsController::class, 'dailyClosing'])->name('logistics.closing');
            Route::get('/logistics/archives', [LogisticsController::class, 'archives'])->name('logistics.archives');
            Route::get('/news', [LogisticsController::class, 'news'])->name('news.index');
            
            // Product Registry
            Route::get('/logistics/registry', [LogisticsController::class, 'articleIndex'])->name('logistics.articles.index');
            Route::get('/logistics/registry/details', [LogisticsController::class, 'articleShow'])->name('logistics.articles.show');
            Route::post('/logistics/registry/update', [LogisticsController::class, 'updateCatalogProduct'])->name('logistics.articles.update_global');

            // CRUD: Create (Must be before wildcard)
            Route::get('/logistics/create', [LogisticsController::class, 'create'])->name('logistics.create');
            Route::post('/logistics/store', [LogisticsController::class, 'store'])->name('logistics.store');
            
            // CRUD: Edit/Update
            Route::get('/logistics/{bl}/edit', [LogisticsController::class, 'edit'])->name('logistics.edit');
            Route::put('/logistics/{bl}', [LogisticsController::class, 'update'])->name('logistics.update');
            Route::delete('/logistics/{bl}', [LogisticsController::class, 'destroy'])->name('logistics.destroy');
            
            // Actions
            Route::put('/logistics/{bl}/status', [LogisticsController::class, 'updateStatus'])->name('logistics.updateStatus');
            Route::post('/logistics/{bl}/note', [LogisticsController::class, 'addNote'])->name('logistics.note');

            // Article Actions
            Route::get('/logistics/article/{article}/edit', [LogisticsController::class, 'editArticleDetails'])->name('logistics.article.edit_details');
            Route::put('/logistics/article/{article}/details', [LogisticsController::class, 'updateArticleDetails'])->name('logistics.article.update_details');
            Route::put('/logistics/article/{article}', [LogisticsController::class, 'updateArticle'])->name('logistics.article.update');
            Route::delete('/logistics/article/{article}', [LogisticsController::class, 'destroyArticle'])->name('logistics.article.delete');
            
            // --- Dashboard ---
            Route::get('/logistics', [LogisticsController::class, 'dashboard'])->name('logistics.index');

            // --- SAV / Incidents ---
            Route::get('/sav', [LogisticsController::class, 'incidents'])->name('sav.index');
            Route::post('/sav/store', [LogisticsController::class, 'storeIncident'])->name('sav.store');

            // --- WILDCARD ROUTE (MUST BE LAST) ---
            Route::get('/logistics/{bl}', [LogisticsController::class, 'show'])->name('logistics.show');
        });
    });


    // ====================================================
    // D. GLOBAL RESOURCES
    // ====================================================
    Route::get('/catalog', [SalesCockpitController::class, 'catalog'])->name('products.catalog');
    Route::get('/documentation', function () { return view('documentation'); })->name('documentation');

}); // End Auth Group


// --- 4. AUTH ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});