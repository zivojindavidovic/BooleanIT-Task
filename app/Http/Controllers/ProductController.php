<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductException;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

/**
 * @OA\Info(
 *     title="BooleanIT-Task API Documentation",
 *     version="1.0.0",
 *     description="API documentation for BooleanIT task"
 * )
 * @OA\Tag(
 *     name="Products",
 *     description="Endpoints related to products"
 * )
 */
class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get all products.
     *
     * @OA\Get(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="Get all products",
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
     *         description="Number of products per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=25)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function getAllProducts(Request $request): JsonResponse
    {
        $pagination = [
            'page' => $request->input('page', 1),
            'per_page' => $request->input('per_page', 25),
        ];

        return response()->json($this->productService->getAllProducts($pagination));
    }

    /**
     * Update a product.
     *
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"regular_price"},
     *             @OA\Property(property="regular_price", type="number", format="float", example=999.99),
     *             @OA\Property(property="sale_price", type="number", format="float", nullable=true, example=799.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function updateProduct(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'regular_price' => 'nullable|numeric|min:1',
                'sale_price' => 'nullable|numeric|min:1',
            ]);

            $product = Product::findOrFail($id);

            $data = [
                'regular_price' => $request->input('regular_price', $product->regular_price),
                'sale_price' => $request->input('sale_price', $product->sale_price),
            ];

            $result = $this->productService->updateProduct($product, $data);

            return response()->json($result);
        } catch (ValidationException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }
    }

    /**
     * Delete a product.
     *
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product 1 has been deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function deleteProduct($id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);

            return response()->json([
                'message' => "Product $id has been deleted successfully"
            ]);
        } catch (ProductException $ex) {
            return $ex->render();
        }
    }
}
