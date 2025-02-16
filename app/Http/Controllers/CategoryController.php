<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function getAllCategories(Request $request): JsonResponse
    {
        $pagination = [
            'page' => $request->input('page', 1),
            'per_page' => $request->input('per_page', 25),
        ];

        $categories = $this->categoryService->getAllCategories($pagination);

        return response()->json($categories);
    }
}
