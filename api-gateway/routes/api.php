<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

/**
 * API маршруты для продуктов.
 */
Route::prefix('products')->middleware([\App\Http\Middleware\LogRequests::class])->group(function () {
    Route::get('/', [ProductController::class, 'index']); // Список продуктов
    Route::post('/', [ProductController::class, 'store']); // Создать продукт
    Route::get('/{id}', [ProductController::class, 'show']); // Получить продукт по ID
    Route::put('/{id}', [ProductController::class, 'update']); // Обновить продукт
    Route::delete('/{id}', [ProductController::class, 'destroy']); // Удалить продукт
});

/**
 * API маршруты для заказов.
 */
Route::prefix('orders')->middleware([\App\Http\Middleware\LogRequests::class])->group(function () {
    Route::get('/search-by-product', [OrderController::class, 'searchByProduct']); // поиск по товару
    Route::get('/', [OrderController::class, 'index']); // Список заказов
    Route::post('/', [OrderController::class, 'store']); // Создать заказ
    Route::get('/{id}', [OrderController::class, 'show']); // Получить заказ по ID
    Route::put('/{id}', [OrderController::class, 'update']); // Обновить заказ
    Route::delete('/{id}', [OrderController::class, 'destroy']); // Удалить заказ
});
