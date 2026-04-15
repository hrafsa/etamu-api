<?php

use App\Http\Controllers\Api\Auth\ApiLoginController;
use App\Http\Controllers\Api\Auth\ApiRegisterController;
use App\Http\Controllers\Api\PengajuanController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public API endpoints (JSON only)
Route::middleware(['force.json','api.normalize'])->group(function () {
    // Auth (guest)
    Route::middleware('guest')->group(function () {
        Route::post('/register', [ApiRegisterController::class, 'register'])
            ->middleware(['throttle:5,1'])
            ->name('api.register');

        Route::post('/login', [ApiLoginController::class, 'login'])
            ->middleware(['throttle:5,1'])
            ->name('api.login');
    });

    // Categories accessible without auth for mobile onboarding
    Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
    Route::get('/categories/{category}/sub-categories', [CategoryController::class, 'subCategories'])->name('api.categories.sub');
});

// Authenticated routes (bearer Sanctum, users only)
Route::middleware(['force.json','api.normalize','api.auth'])->group(function () {
    Route::post('/logout', [ApiLoginController::class, 'logout'])->name('api.logout');

    // Profile info
    Route::get('/profile', function (Request $request) {
        return response()->json([
            'status' => true,
            'user' => $request->user(),
        ]);
    })->name('api.profile');

    // Update profile and password
    Route::patch('/profile', [ProfileController::class, 'update'])->name('api.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->middleware(['throttle:10,1'])
        ->name('api.profile.password');

    // Pengajuan endpoints
    Route::get('/pengajuan/years', [PengajuanController::class, 'years'])->name('api.pengajuan.years');
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('api.pengajuan.index');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('api.pengajuan.store');
    Route::get('/pengajuan/{nomor}', [PengajuanController::class, 'show'])->name('api.pengajuan.show');
});

// Fallback for unknown API endpoints (must be last)
Route::fallback(function () {
    return response()->json([
        'status' => false,
        'message' => 'Endpoint not found.'
    ], 404);
})->name('api.fallback');
