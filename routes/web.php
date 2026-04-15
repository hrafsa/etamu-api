<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PengajuanAdminController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin (web) protected area
Route::middleware(['auth', 'verified', 'isAdmin'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Categories + Sub Categories
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::post('/admin/categories/{category}/sub', [CategoryController::class, 'storeSub'])->name('admin.categories.sub.store');
    Route::delete('/admin/categories/{category}/sub/{subCategory}', [CategoryController::class, 'destroySub'])->name('admin.categories.sub.destroy');

    // Pengajuan admin management
    Route::get('/admin/pengajuan', [PengajuanAdminController::class, 'index'])->name('admin.pengajuan.index');
    Route::get('/admin/pengajuan/{pengajuan}', [PengajuanAdminController::class, 'show'])->name('admin.pengajuan.show');
    Route::patch('/admin/pengajuan/{pengajuan}/status', [PengajuanAdminController::class, 'updateStatus'])->name('admin.pengajuan.status');

    // User management resource (index, edit, update, destroy) - no create/store per requirements
    Route::resource('user-management', UserManagementController::class)
        ->only(['index', 'edit', 'update', 'destroy'])
        ->parameters(['user-management' => 'user']);


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
