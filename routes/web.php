<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\EmployeeController;

Route::get('/', [WelcomeController::class, 'index']);

Route::middleware(['auth', 'admin.only'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

Route::middleware(['auth', 'creator.only'])->group(function () {
    Route::get('/creator', [CreatorController::class, 'index']);
});

Route::middleware(['auth', 'employee.only'])->group(function () {
    Route::get('/employee', [EmployeeController::class, 'index']);
});
