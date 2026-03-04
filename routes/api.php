<?php

use App\Http\Controllers\Api\Emploes\AuthEmploesController;
use App\Http\Controllers\api\emploes\DiriktorController;
use App\Http\Controllers\api\user\{AuthController,CompanyController,OrderController};
use Illuminate\Support\Facades\Route;

Route::prefix('v1/employees')->group(function () {
    Route::post('login', [AuthEmploesController::class, 'login'])->name('api.v1.employees.login');
    Route::middleware(['auth:sanctum', 'role:director,courier'])->group(function () {
        Route::get('profile', [AuthEmploesController::class, 'profile'])->name('api.v1.employees.profile');
        Route::post('session-check', [AuthEmploesController::class, 'sessionCheck'])->name('api.v1.employees.session-check');
        Route::post('logout', [AuthEmploesController::class, 'logout'])->name('api.v1.employees.logout');
    });
    Route::middleware(['auth:sanctum', 'role:director'])->group(function () {
        Route::get('setting', [DiriktorController::class, 'setting'])->name('api.v1.employees.setting'); // Firma sozlamalari
        Route::post('setting/create/emploes', [DiriktorController::class, 'setting_create_emploes'])->name('api.v1.employees.setting.create.emploes'); // Yangi hodim qo'shish
        Route::post('setting/update-status', [DiriktorController::class, 'updateStatus'])->name('api.v1.setting.update-status'); // Hodim statusini yangilash
        Route::post('setting/company/update-status', [DiriktorController::class, 'updateCompanyStatus'])->name('api.v1.setting.company.update-status'); // Firma statusini yangilash
        Route::post('setting/product/update-status', [DiriktorController::class, 'updateProductStatus'])->name('api.v1.setting.product.update-status'); // product statusini yangilash
    });
});

Route::prefix('v1/user')->group(function () {
    Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']); // SMS yuborish
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']); // SMS tasdiqlash
    Route::get('companies', [CompanyController::class, 'allCompany']); // Firmalar
    Route::get('companiee/{id}', [CompanyController::class, 'companyShow']); // Firma haqida
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']); // Profile
        Route::get('/auth/check', [AuthController::class, 'checkToken']); // Tokenni tekshirish
        Route::post('/profile/update', [AuthController::class, 'updateProfile']); // Profile Update
        Route::get('/orders', [OrderController::class, 'index']); // Barcha buyurtmalar
        Route::get('/orders/{id}', [OrderController::class, 'show']); // Buyurtma haqida
        Route::post('/orders', [OrderController::class, 'store']); // Yangi buyurtma
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']); // Buyurtmani bekor qilish
    });
});