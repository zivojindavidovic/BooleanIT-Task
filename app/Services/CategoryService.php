<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryService
{
    public function getAllCategories(array $pagination): Collection;
    public function updateCategory(int $categoryId, string $categoryName): Category;
    public function getCategoryProducts(int $categoryId, array $pagination): Collection;
    public function deleteCategory(int $categoryId): void;
    public function exportCategoryProducts(int $categoryId): array;
}
