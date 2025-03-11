<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\SKUController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\RackController;
use App\Http\Controllers\Admin\StorackController;
use App\Http\Controllers\GuestController;
use App\Http\Middleware\AdminAuth;

// Guest Routes
Route::get('/', [GuestController::class, 'index'])->name('dashboard');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');

// Admin Routes
Route::prefix('admin')->group(function() {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

    Route::middleware([AdminAuth::class])->group(function() {
        Route::get('/dashboard', [AuthController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

        Route::get('/zone/list', [ZoneController::class, 'index'])->name('zone.list');
        Route::get('/zone/create', [ZoneController::class, 'showCreateForm'])->name('zone.create');
        Route::post('/zone/create', [ZoneController::class, 'create'])->name('zone.create.submit');
        Route::get('/zone/update/{id}', [ZoneController::class, 'showUpdateForm'])->name('zone.update');
        Route::put('/zone/update/{id}', [ZoneController::class, 'update'])->name('zone.update.submit');
        Route::delete('/zone/delete/{id}', [ZoneController::class, 'destroy'])->name('zone.destroy');

        Route::get('/rack/list', [RackController::class, 'index'])->name('rack.list');
        Route::get('/rack/create', [RackController::class, 'showCreateForm'])->name('rack.create');
        Route::post('/rack/create', [RackController::class, 'create'])->name('rack.create.submit');
        Route::get('/rack/update/{id}', [RackController::class, 'showUpdateForm'])->name('rack.update');
        Route::put('/rack/update/{id}', [RackController::class, 'update'])->name('rack.update.submit');
        Route::delete('/rack/delete/{id}', [RackController::class, 'destroy'])->name('rack.destroy');

        Route::get('/storack/list', [StorackController::class, 'index'])->name('storack.list');
        Route::get('/storack/create', [StorackController::class, 'showCreateForm'])->name('storack.create');
        Route::post('/storack/create', [StorackController::class, 'create'])->name('storack.create.submit');
        Route::get('/storack/update/{id}', [StorackController::class, 'showUpdateForm'])->name('storack.update');
        Route::put('/storack/update/{id}', [StorackController::class, 'update'])->name('storack.update.submit');
        Route::delete('/storack/delete/{id}', [StorackController::class, 'destroy'])->name('storack.destroy');

        Route::get('/sku/list', [SKUController::class, 'index'])->name('admin.dashboard');
        Route::get('/sku/create', [SKUController::class, 'showCreateForm'])->name('sku.create');
        Route::post('/sku/create', [SKUController::class, 'create'])->name('sku.create.submit');
        Route::get('/sku/update/{id}', [SKUController::class, 'showUpdateForm'])->name('sku.update');
        Route::put('/sku/update/{id}', [SKUController::class, 'update'])->name('sku.update.submit');
        Route::delete('/sku/delete/{id}', [SKUController::class, 'destroy'])->name('sku.destroy');
    });
});
