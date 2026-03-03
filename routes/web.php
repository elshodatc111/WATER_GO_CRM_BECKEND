<?php

use App\Http\Controllers\web\{AuthController, CompanyController, HomeController, ProductController};
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
    Route::POST('/company/emploes/create', [CompanyController::class, 'storeEmployee'])->name('companye_create_emploes');
    Route::POST('/company/emploes/toggle-status', [CompanyController::class, 'toggleEmployeeStatus'])->name('companye_emploes_toggle_status');
    Route::POST('/company/emploes/resset-password', [CompanyController::class, 'resetEmployeePassword'])->name('companye_emploes_resset_password');
    Route::delete('/company/emploes/delete', [CompanyController::class, 'deleteEmployee'])->name('companye_emploes_delete');

    Route::post('/product/create', [ProductController::class, 'store'])->name('product_create');
    Route::post('/product/toggle_status', [ProductController::class, 'toggleProductStatus'])->name('product_toggle_status');
    Route::delete('/product/delete', [ProductController::class, 'deleteProduct'])->name('product_delete');
    Route::get('/product/show/{id}', [ProductController::class, 'show'])->name('product_show'); // 
    Route::post('/product/update', [ProductController::class, 'update'])->name('product_update');
    Route::post('/product/update/image', [ProductController::class, 'updateImage'])->name('product_update_image');
    Route::post('/product/update/banner', [ProductController::class, 'updateBanner'])->name('product_update_banner');

});