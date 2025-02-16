<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Exceptions\CategoryException;
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

    /**
     * @throws CategoryException
     */
    public function deleteCategory(int $categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $isCategoryDeleted = $category->status == StatusEnum::DELETED->value;

        if ($isCategoryDeleted) {
            throw new CategoryException("Category has already been deleted", 400);
        }

        $categoryHasActiveProducts = $category->products()
            ->where('status', StatusEnum::ACTIVE->value)
            ->count();

        if ($categoryHasActiveProducts > 0) {
            throw new CategoryException("Category has active products", 400);
        }

        $category->status = StatusEnum::DELETED;
        $category->save();
    }
}
