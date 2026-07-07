<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

// Public APIs (Không cần đăng nhập)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::apiResource('tasks', TaskController::class);

// Public: Xem danh mục, Xem sản phẩm
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Protected APIs (Bắt buộc đăng nhập)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', [AuthController::class, 'logout']);
    
    // API Giỏ hàng
    Route::get('cart', [\App\Http\Controllers\CartController::class, 'index']);
    Route::post('cart', [\App\Http\Controllers\CartController::class, 'add']);
    Route::delete('cart/{itemId}', [\App\Http\Controllers\CartController::class, 'remove']);

    // API Đặt hàng (từ giỏ hàng)
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);

    // Protected APIs (Chỉ dành cho Admin)
    Route::middleware('is_admin')->group(function () {
        // Thêm/Sửa/Xóa Danh mục và Sản phẩm
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    });
});
