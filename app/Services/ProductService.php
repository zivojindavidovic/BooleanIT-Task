<?php

namespace App\Services;

use App\Models\Product;

interface ProductService
{
    public function getAllProducts(array $pagination);
    public function updateProduct(Product $product, array $data);
    public function deleteProduct(int $id);
}
