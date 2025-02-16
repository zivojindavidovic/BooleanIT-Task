<?php

namespace App\Services;

interface CategoryService
{
    public function getAllCategories(array $pagination);
    public function updateCategory(int $categoryId, string $categoryName);
    public function getCategoryProducts(int $categoryId, array $pagination);
}
