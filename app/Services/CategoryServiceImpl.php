<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Exceptions\CategoryException;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use League\Csv\Writer;
use SplTempFileObject;

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

    public function exportCategoryProducts(int $categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $products = Product::with(['category', 'department', 'manufacturer'])
            ->where('category_id', $categoryId)
            ->get();

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['product_id','product_number','sku','regular_price','sale_price','department_name','manufacturer_name']);

        foreach ($products as $p) {
            $csv->insertOne([
                $p->product_id,
                $p->product_number,
                $p->sku,
                $p->regular_price,
                $p->sale_price,
                $p->department->department_name,
                $p->manufacturer->manufacturer_name
            ]);
        }

        $adjustedCategoryName = preg_replace('/[^a-zA-Z0-9]+/', '_', $category->category_name);

        $now = Carbon::now()->format('Y_m_d-H_i');
        $fileName = "{$adjustedCategoryName}_{$now}.csv";

        $csvContents = $csv->toString();

        return [
            'fileName' => $fileName,
            'csvContents' => $csvContents
        ];
    }
}
