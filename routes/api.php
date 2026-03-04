<?php

use App\Http\Controllers\Api\Emploes\AuthEmploesController;
use App\Http\Controllers\api\emploes\DiriktorController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/employees')->group(function () {
    Route::post('login', [AuthEmploesController::class, 'login'])->name('api.v1.employees.login');
    Route::middleware(['auth:sanctum', 'role:director,courier'])->group(function () {
        Route::get('profile', [AuthEmploesController::class, 'profile'])->name('api.v1.employees.profile');
        Route::post('session-check', [AuthEmploesController::class, 'sessionCheck'])->name('api.v1.employees.session-check');
        Route::post('logout', [AuthEmploesController::class, 'logout'])->name('api.v1.employees.logout');
    });
    Route::middleware(['auth:sanctum', 'role:director'])->group(function () {
        Route::get('setting', [DiriktorController::class, 'setting'])->name('api.v1.employees.setting');
        Route::post('setting/create/emploes', [DiriktorController::class, 'setting_create_emploes'])->name('api.v1.employees.setting.create.emploes');
        Route::post('setting/update-status', [DiriktorController::class, 'updateStatus'])->name('api.v1.setting.update-status');
    });
});