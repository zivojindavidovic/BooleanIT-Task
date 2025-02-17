<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'getAllCategories']);
        Route::put('/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('/{id}', [CategoryController::class, 'deleteCategory']);
        Route::get('/{id}/products', [CategoryController::class, 'getCategoryProducts']);
        Route::get('/{id}/products/export', [CategoryController::class, 'exportCategoryProducts']);
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'getAllProducts']);
        Route::put('/{id}', [ProductController::class, 'updateProduct']);
        Route::delete('/{id}', [ProductController::class, 'deleteProduct']);
    });
});
