<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductController::class)->middleware('api');

Route::post('/products/exist', [ProductController::class, 'checkExistence']);
