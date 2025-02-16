<?php

namespace App\Services;

interface ProductService
{
    public function getAllProducts(array $pagination);
    public function deleteProduct(int $id);
}
