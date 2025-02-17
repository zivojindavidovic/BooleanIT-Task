<?php

namespace App\Http\Controllers;

use App\Exceptions\CategoryException;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Endpoints related to categories"
 * )
 */
class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get all categories.
     *
     * @OA\Get(
     *     path="/api/v1/categories",
     *     tags={"Categories"},
     *     summary="Get all categories",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of categories per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=25)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Category"))
     *     )
     * )
     */
    public function getAllCategories(Request $request): JsonResponse
    {
        $pagination = [
            'page' => $request->input('page', 1),
            'per_page' => $request->input('per_page', 25),
        ];

        $categories = $this->categoryService->getAllCategories($pagination);

        return response()->json($categories);
    }

    /**
     * Update a category.
     *
     * @OA\Put(
     *     path="/api/v1/categories/{categoryId}",
     *     tags={"Categories"},
     *     summary="Update a category",
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category_name"},
     *             @OA\Property(property="category_name", type="string", example="Updated Category Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function updateCategory(Request $request, $categoryId): JsonResponse
    {
        $validated = $request->validate([
            'category_name' => 'required|string'
        ]);

        $result = $this->categoryService->updateCategory($categoryId, $validated['category_name']);

        return response()->json($result);
    }

    /**
     * Get products in a category.
     *
     * @OA\Get(
     *     path="/api/v1/categories/{categoryId}/products",
     *     tags={"Categories"},
     *     summary="Get products in a category",
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function getCategoryProducts(Request $request, $categoryId): JsonResponse
    {
        $pagination = [
            'page' => $request->input('page', 1),
            'per_page' => $request->input('per_page', 25),
        ];

        return response()->json($this->categoryService->getCategoryProducts($categoryId, $pagination));
    }

    /**
     * Delete a category.
     *
     * @OA\Delete(
     *     path="/api/v1/categories/{categoryId}",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category 1 deleted successfully")
     *         )
     *     )
     * )
     */
    public function deleteCategory($categoryId): JsonResponse
    {
        try {
            $this->categoryService->deleteCategory($categoryId);

            return response()->json([
                'message' => "Category $categoryId deleted successfully"
            ]);
        } catch (CategoryException $ex) {
            return $ex->render();
        }
    }

    /**
     * Export products in a category as a CSV file.
     *
     * @OA\Get(
     *     path="/api/v1/categories/{categoryId}/products/export",
     *     tags={"Categories"},
     *     summary="Export category products as CSV",
     *     description="Exports all products in a given category as a downloadable CSV file.",
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="CSV file download",
     *         @OA\Header(
     *             header="Content-Disposition",
     *             description="attachment; filename=category_products.csv",
     *             @OA\Schema(type="string", example="attachment; filename=category_products.csv")
     *         ),
     *         @OA\MediaType(
     *             mediaType="text/csv",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function exportCategoryProducts($categoryId)
    {
        $result = $this->categoryService->exportCategoryProducts($categoryId);

        $csvContents = $result['csvContents'];
        $fileName = $result['fileName'];

        return response($csvContents, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }
}
