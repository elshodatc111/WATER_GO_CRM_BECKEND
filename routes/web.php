<?php

use App\Http\Controllers\web\{AuthController, CompanyController, HomeController};
use Illuminate\Support\Facades\Route;

Route::get('/login',[AuthController::class, 'showLogin'])->name('login');
Route::post('/login/check',[AuthController::class, 'login'])->name('login_check');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/companyee', [CompanyController::class, 'companyee'])->name('companyee');
    Route::POST('/company/create', [CompanyController::class, 'store'])->name('companye_create');
    Route::get('/companye/show/{id}', [CompanyController::class, 'show'])->name('companye_show');
    Route::POST('/company/update', [CompanyController::class, 'update'])->name('companye_update');
    Route::POST('/company/update/logo', [CompanyController::class, 'updateLogo'])->name('companye_update_logo');
    Route::POST('/company/update/banner', [CompanyController::class, 'updateBanner'])->name('companye_update_banner');
    Route::POST('/company/update/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companye_update_toggle_status');

});