<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [CategoryController::class, 'getAllCategories']);
Route::put('/categories/{id}', [CategoryController::class, 'updateCategory']);
Route::delete('/categories/{id}', [CategoryController::class, 'deleteCategory']);
Route::get('/categories/{id}/products', [CategoryController::class, 'getCategoryProducts']);

Route::get('/products', [ProductController::class, 'getAllProducts']);
Route::put('products/{id}', [ProductController::class, 'deleteProduct']);
