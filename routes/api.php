<?php

use App\Http\Controllers\CommissionReportController;
use App\Http\Controllers\TopDistributorsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Commission Report Routes
    Route::get('/commission-report', [CommissionReportController::class, 'index'])
        ->name('api.commission-report.index');
    
    Route::get('/commission-report/order/{orderId}', [CommissionReportController::class, 'show'])
        ->name('api.commission-report.show');

    // Top Distributors Routes
    Route::get('/top-distributors', [TopDistributorsController::class, 'index'])
        ->name('api.top-distributors.index');
});
