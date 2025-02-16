<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductService
{
    public function getAllProducts(array $pagination): Collection;
    public function updateProduct(Product $product, array $data): Product;
    public function deleteProduct(int $id): void;
}
