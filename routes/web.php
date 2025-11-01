<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapPageController;
use App\Http\Controllers\PriceReportController;
use App\Http\Controllers\TrendController;
use App\Http\Controllers\DearthReportController;
use App\Http\Controllers\ReverseGeoCodeController;
use App\Http\Controllers\LocationController;


// Main Page
Route::get('/', [MapPageController::class, 'index'])->name('map.index');

// Location hierarchy routes
Route::get('/regencies/provinces', [LocationController::class, 'getProvinces'])->name('provinces.index');
Route::get('/regencies/{provinceId}', [LocationController::class, 'getRegencies'])->name('regencies.index');
Route::get('/districts/{regencyId}', [LocationController::class, 'getDistricts'])->name('districts.index');
Route::get('/villages/{districtId}', [LocationController::class, 'getVillages'])->name('villages.index');
// Authentication Routes
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

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
    Route::get('/reverse-geocode', [ReverseGeoCodeController::class, 'getLocation'])->name('api.reverse.geocode');
});