<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/dropdown', [CategoryController::class, 'dropdown']);
Route::get('/categories/with-counts', [CategoryController::class, 'withCounts']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);