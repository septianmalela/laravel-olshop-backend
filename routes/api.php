<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckTokenVersion;

// Admin Controller
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware(['auth:api', 'is_admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::controller(ProductCategoryController::class)->group(function () {
            Route::get('product_categories', 'index');
            Route::post('product_categories', 'store');
            Route::get('product_categories/{id}', 'show');
            Route::put('product_categories/{id}', 'update');
            Route::delete('product_categories/{id}', 'destroy');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('products', 'index');
            Route::post('products', 'store');
            Route::get('products/{id}', 'show');
            Route::put('products/{id}', 'update');
            Route::delete('products/{id}', 'destroy');
        });
    });
});
