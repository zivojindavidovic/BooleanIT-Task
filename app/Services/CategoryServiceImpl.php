<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\Category;

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
}
