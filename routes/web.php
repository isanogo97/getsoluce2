<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\InvitationController;

Route::get('/', [WelcomeController::class, 'index']);

Route::middleware(['auth', 'admin.only'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/enterprises/{enterprise}', [EnterpriseController::class, 'show']);
});

Route::middleware(['auth', 'creator.only'])->group(function () {
    Route::get('/creator', [CreatorController::class, 'index'])->name('creator.index');
    Route::get('/creator/enterprises/create', [CreatorController::class, 'create'])->name('creator.create');
    Route::post('/creator/enterprises', [CreatorController::class, 'store'])->name('creator.store');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.index');
});

Route::middleware(['auth', 'employee.only'])->group(function () {
    Route::get('/employee', [EmployeeController::class, 'index']);
    Route::get('/employee/profile', [EmployeeController::class, 'profile'])->name('employee.profile');
});

Route::middleware('auth')->get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

require __DIR__.'/auth.php';
