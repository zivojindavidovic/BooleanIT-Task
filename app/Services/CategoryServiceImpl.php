<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\Category;
use App\Models\Product;

class CategoryServiceImpl implements CategoryService
{

    public function getAllCategories(array $pagination)
    {
        return Category::where('status', StatusEnum::ACTIVE)->paginate(
            perPage: $pagination['per_page'],
            page: $pagination['page']
        );
    }

    public function updateCategory(int $categoryId, string $categoryName)
    {
        $category = Category::findOrFail($categoryId);
        $category->category_name = $categoryName;

        $category->save();

        return $category;
    }

    public function getCategoryProducts(int $categoryId, array $pagination)
    {
        return Product::whereHas('category', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId)
                ->where('status', StatusEnum::ACTIVE);
        })
            ->where('status', StatusEnum::ACTIVE)
            ->paginate(
                perPage: $pagination['per_page'],
                page: $pagination['page']
            );
    }
}
