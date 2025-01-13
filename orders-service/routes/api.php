<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/orders/search-by-product', [OrderController::class, 'searchByProduct'])->name('orders.search-by-product');
Route::apiResource('orders', OrderController::class)->middleware('api');
