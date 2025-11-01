<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapPageController;
use App\Http\Controllers\PriceReportController;
use App\Http\Controllers\TrendController;
use App\Http\Controllers\DearthReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Main Page
Route::get('/', [MapPageController::class, 'index'])->name('map.index');

// Authentication Routes
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| API Routes (Web Context)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    
    // Price Reports API
    Route::prefix('reports')->group(function () {
        Route::post('/', [PriceReportController::class, 'store'])->name('api.reports.store');
        Route::get('/', [PriceReportController::class, 'index'])->name('api.reports.index');
    });
    
    // Trend API
    Route::get('/trend', [TrendController::class, 'trend'])->name('api.trend');
    
    // Dearth Reports API (Kelangkaan)
    Route::prefix('dearth')->group(function () {
        Route::post('/reports', [DearthReportController::class, 'store'])->name('api.dearth.store');
        Route::get('/map', [DearthReportController::class, 'getDearthMap'])->name('api.dearth.map');
        Route::get('/kabupaten', [DearthReportController::class, 'getByKabupaten'])->name('api.dearth.kabupaten');
        Route::get('/recent', [DearthReportController::class, 'getRecent'])->name('api.dearth.recent');
    });
});