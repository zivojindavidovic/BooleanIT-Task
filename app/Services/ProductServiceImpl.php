<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Exceptions\ProductException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductServiceImpl implements ProductService
{
    public function getAllProducts(array $pagination): Collection
    {
        return Product::where('status', StatusEnum::ACTIVE)
            ->simplePaginate(
                perPage: $pagination['per_page'],
                page: $pagination['page']
            )->getCollection();
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $product->regular_price = $data['regular_price'];
        $product->sale_price = $data['sale_price'];

        $product->save();

        return $product;
    }

    /**
     * @throws ProductException
     */
    public function deleteProduct(int $id): void
    {
        $product = Product::findOrFail($id);

        if ($product['is_deleted']) {
            throw new ProductException("Product has already been deleted", 400);
        }

        $product->status = StatusEnum::DELETED;
        $product->save();
    }
}
