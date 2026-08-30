<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [PublicController::class, 'index'])->name('home');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard (requires email verification)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('admin.dashboard');

    // Admin Panel Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('verified')
            ->name('admin.dashboard');

        Route::resources([
            'products' => ProductController::class,
            'categories' => CategoryController::class,
            'suppliers' => SupplierController::class,
            'sales' => SaleController::class,
        ]);

        // Inventory Routes
        Route::controller(InventoryController::class)->prefix('inventory')->name('inventory.')->group(function () {
            // Inventory Stock Receipts
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/receipts', 'receipts')->name('receipts');
            Route::delete('/receipts/{receipt}', 'destroyReceipt')->name('receipts.destroy');

            // Inventory Items
            Route::prefix('items')->name('items.')->group(function () {
                Route::get('/', 'itemsIndex')->name('index');
                Route::get('/create', 'itemsCreate')->name('create');
                Route::post('/', 'itemsStore')->name('store');
                Route::get('/{item}/edit', 'itemsEdit')->name('edit');
                Route::put('/{item}', 'itemsUpdate')->name('update');
                Route::delete('/{item}', 'itemsDestroy')->name('destroy');
            });
        });
    });
});

require __DIR__ . '/settings.php';