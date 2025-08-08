<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckTokenVersion;

// Global Controller(without login)
use App\Http\Controllers\ProductController;

// Admin Controller
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;

// User Controller
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\OrderController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::get('/products', [ProductController::class, 'index']);

Route::prefix('user')->group(function () {
    Route::middleware(['auth:api', 'is_user'])->group(function () {
        Route::controller(CartController::class)->group(function () {
            Route::get('carts', 'index');
            Route::post('add_to_cart', 'add_to_cart');
            Route::delete('delete_cart_item/{id}', 'delete_cart_item');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('orders', 'index');
            Route::post('create_order', 'create_order');
            Route::get('orders/{id}', 'show');
        });
    });
});

Route::prefix('admin')->group(function () {
    Route::controller(AdminAuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
    });

    Route::middleware(['auth:api', 'is_admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::controller(ProductCategoryController::class)->group(function () {
            Route::get('product_categories', 'index');
            Route::post('product_categories', 'store');
            Route::get('product_categories/{id}', 'show');
            Route::put('product_categories/{id}', 'update');
            Route::delete('product_categories/{id}', 'destroy');
        });

        Route::controller(AdminProductController::class)->group(function () {
            Route::get('products', 'index');
            Route::post('products', 'store');
            Route::get('products/{id}', 'show');
            Route::put('products/{id}', 'update');
            Route::delete('products/{id}', 'destroy');
        });
    });
});
