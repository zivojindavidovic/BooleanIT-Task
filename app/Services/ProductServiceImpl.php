<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\Product;

class ProductServiceImpl implements ProductService
{
    public function getAllProducts(array $pagination)
    {
        return Product::where('status', StatusEnum::ACTIVE)->paginate(
            perPage: $pagination['per_page'],
            page: $pagination['page']
        );
    }
}
