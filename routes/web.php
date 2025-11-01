<?php

use App\Http\Controllers\Admin\CommodityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerifyReportController;
use App\Http\Controllers\Admin\VillageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapPageController;
use App\Http\Controllers\PriceReportController;
use App\Http\Controllers\TrendController;
use App\Http\Controllers\DearthReportController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ReverseGeocodeController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Auth;

// Main Page
Route::get('/', [MapPageController::class, 'index'])->name('map.index');

// Location hierarchy routes
Route::get('/regencies/provinces', [LocationController::class, 'getProvinces'])->name('provinces.index');
Route::get('/regencies/{provinceId}', [LocationController::class, 'getRegencies'])->name('regencies.index');
Route::get('/districts/{regencyId}', [LocationController::class, 'getDistricts'])->name('districts.index');
Route::get('/villages/{districtId}', [LocationController::class, 'getVillages'])->name('villages.index');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home')
    ->middleware(['auth', 'role:ADMIN']);

Route::middleware(['auth', 'role:ADMIN'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/verify-reports',           [VerifyReportController::class, 'index'])->name('verify-reports.index');
        Route::get('/verify-reports/data',      [VerifyReportController::class, 'getData'])->name('verify-reports.data');
        Route::get('/verify-reports/{id}',      [VerifyReportController::class, 'show'])->name('verify-reports.show');
        Route::put('/verify-reports/{id}',      [VerifyReportController::class, 'updateStatus'])->name('verify-reports.update');
        Route::delete('/verify-reports/{id}',   [VerifyReportController::class, 'destroy'])->name('verify-reports.destroy');

        // --- Users (CRUD user; hanya admin) ---
        Route::get('/users',            [UserController::class, 'index'])->name('users.index');
        Route::get('/users/data',       [UserController::class, 'getData'])->name('users.data'); // DataTables
        Route::post('/users',           [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit',  [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}',       [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}',    [UserController::class, 'destroy'])->name('users.destroy');

        // --- Komoditas (CRUD) ---
        Route::get('/commodities', [CommodityController::class, 'index'])
            ->name('commodities.index');

        // DataTables (AJAX)
        Route::get('/commodities/data', [CommodityController::class, 'getData'])
            ->name('commodities.data');

        // Simpan baru
        Route::post('/commodities', [CommodityController::class, 'store'])
            ->name('commodities.store');

        // Edit (ambil data untuk modal)
        Route::get('/commodities/{id}/edit', [CommodityController::class, 'edit'])
            ->name('commodities.edit');

        // Update
        Route::put('/commodities/{id}', [CommodityController::class, 'update'])
            ->name('commodities.update');

        // Hapus
        Route::delete('/commodities/{id}', [CommodityController::class, 'destroy'])
            ->name('commodities.destroy');

        // --- Desa (CRUD) ---
        Route::get('/villages', [VillageController::class, 'index'])->name('villages.index');
        Route::get('/villages/data', [VillageController::class, 'getData'])->name('villages.data');
        Route::post('/villages', [VillageController::class, 'store'])->name('villages.store');
        Route::get('/villages/{id}/edit', [VillageController::class, 'edit'])->name('villages.edit');
        Route::put('/villages/{id}', [VillageController::class, 'update'])->name('villages.update');
        Route::delete('/villages/{id}', [VillageController::class, 'destroy'])->name('villages.destroy');
    });

// API Routes
Route::prefix('api')->group(function () {

    // Price Reports API
    Route::prefix('reports')->group(function () {
        Route::post('/', [PriceReportController::class, 'store'])->name('api.reports.store');
        Route::get('/', [PriceReportController::class, 'index'])->name('api.reports.index');
    });

    // Trend API
    Route::get('/trend', [TrendController::class, 'trend'])->name('api.trend');

    // Dearth Reports API
    Route::prefix('dearth')->group(function () {
        Route::post('/reports', [DearthReportController::class, 'store'])->name('api.dearth.store');
        Route::get('/map', [DearthReportController::class, 'getDearthMap'])->name('api.dearth.map');
        Route::get('/recent', [DearthReportController::class, 'getRecent'])->name('api.dearth.recent');
    });

    // Reverse Geocoding API
    Route::get('/reverse-geocode', [ReverseGeocodeController::class, 'getLocation'])->name('api.reverse.geocode');
});
