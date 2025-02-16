<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [CategoryController::class, 'getAllCategories']);
Route::put('/categories/{id}', [CategoryController::class, 'updateCategory']);
Route::get('/categories/{id}/products', [CategoryController::class, 'getCategoryProducts']);
