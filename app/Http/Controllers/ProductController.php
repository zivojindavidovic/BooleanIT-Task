<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductException;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getAllProducts(Request $request): JsonResponse
    {
        $pagination = [
            'page' => $request->input('page', 1),
            'per_page' => $request->input('per_page', 25),
        ];

        return response()->json($this->productService->getAllProducts($pagination));
    }

    public function updateProduct(Request $request, $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $data = [
            'regular_price' => $request->input('regular_price', $product->regular_price),
            'sale_price' => $request->input('sale_price', $product->sale_price),
        ];

        $result = $this->productService->updateProduct($product, $data);

        return response()->json($result);
    }

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
