<?php

use App\Http\Controllers\api\emploes\AuthEmploesController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('employees')->group(function () {
        Route::post('/login', [AuthEmploesController::class, 'login'])->name('api.employees.login');
        Route::middleware(['auth:sanctum', 'role:director,courier'])->group(function () {
            Route::get('/profile', [AuthEmploesController::class, 'profile'])->name('api.employees.profile');
            Route::post('/logout', [AuthEmploesController::class, 'logout'])->name('api.employees.logout');
            Route::post('/session-check', [AuthEmploesController::class, 'sessionCheck'])->name('api.employees.session-check');
        });
    });

});