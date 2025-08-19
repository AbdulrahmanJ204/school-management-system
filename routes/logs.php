<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Daily Reports
    Route::get('/daily-reports', [LogController::class, 'getDailyReports']);
    Route::post('/daily-reports/generate', [LogController::class, 'generateDailyReport']);
    Route::get('/daily-reports/{id}/pdf', [LogController::class, 'downloadPdfReport']);
    Route::get('/daily-reports/{id}/excel', [LogController::class, 'downloadExcelReport']);
    
    // Filters
    Route::get('/log-filters/user-types', [LogController::class, 'getUserTypes']);
    Route::get('/log-filters/table-names', [LogController::class, 'getTableNames']);
    
    // Maintenance
    Route::post('/logs/clean', [LogController::class, 'cleanOldLogs']);
});
