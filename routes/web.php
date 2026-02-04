<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InterventionController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SiteController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware(['auth.jwt'])->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::get('/sites', [SiteController::class, 'index']);
        Route::get('/sites/{site}', [SiteController::class, 'show']);

        Route::get('/interventions', [InterventionController::class, 'index']);
        Route::get('/interventions/{intervention}', [InterventionController::class, 'show']);
        Route::post('/interventions', [InterventionController::class, 'store']);
        Route::patch('/interventions/{intervention}', [InterventionController::class, 'update']);
        Route::post('/interventions/{intervention}/media', [MediaController::class, 'store']);

        Route::middleware(['role:ROLE_MANAGER'])->group(function () {
            Route::post('/sites/import', [SiteController::class, 'import']);
            Route::get('/reports/weekly', [ReportController::class, 'weekly']);
            Route::get('/reports/weekly/export.csv', [ReportController::class, 'exportCsv']);
            Route::get('/reports/weekly/export.pdf', [ReportController::class, 'exportPdf']);
        });
    });
});
