<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Exceptions\ProductException;
use App\Models\Product;
use function Symfony\Component\Translation\t;

class ProductServiceImpl implements ProductService
{
    public function getAllProducts(array $pagination)
    {
        return Product::where('status', StatusEnum::ACTIVE)->paginate(
            perPage: $pagination['per_page'],
            page: $pagination['page']
        );
    }

    public function updateProduct(Product $product, array $data)
    {
        $product->regular_price = $data['regular_price'];
        $product->sale_price = $data['sale_price'];

        $product->save();

        return $product;
    }

    public function deleteProduct(int $id)
    {
        $product = Product::findOrFail($id);

        $isProductDeleted = $product->status == StatusEnum::DELETED->value;
        if ($isProductDeleted) {
            throw new ProductException("Product has already been deleted", 400);
        }

        $product->status = StatusEnum::DELETED;
        $product->save();
    }
}
