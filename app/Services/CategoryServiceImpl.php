<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Exceptions\CategoryException;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use League\Csv\Writer;
use SplTempFileObject;

class CategoryServiceImpl implements CategoryService
{

    public function getAllCategories(array $pagination): Collection
    {
        return Category::where('status', StatusEnum::ACTIVE)
            ->simplePaginate(
                perPage: $pagination['per_page'],
                page: $pagination['page']
            )->getCollection();
    }

    public function updateCategory(int $categoryId, string $categoryName): Category
    {
        $category = Category::findOrFail($categoryId);
        $category->category_name = $categoryName;

        $category->save();

        return $category;
    }

    public function getCategoryProducts(int $categoryId, array $pagination): Collection
    {
        return Product::whereHas('category', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId)
                ->where('status', StatusEnum::ACTIVE);
        })
            ->where('status', StatusEnum::ACTIVE)
            ->simplePaginate(
                perPage: $pagination['per_page'],
                page: $pagination['page']
            )->getCollection();
    }

    /**
     * @throws CategoryException
     */
    public function deleteCategory(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        if ($category['is_deleted']) {
            throw new CategoryException("Category has already been deleted", 400);
        }

        if ($category['active_products'] > 0) {
            throw new CategoryException("Category has active products", 400);
        }

        $category->status = StatusEnum::DELETED;
        $category->save();
    }

    public function exportCategoryProducts(int $categoryId): array
    {
        $category = Category::findOrFail($categoryId);
        $products = Product::with(['category', 'department', 'manufacturer'])
            ->where('category_id', $categoryId)
            ->get();

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['product_id','product_number', 'upc', 'sku','regular_price','sale_price','department_name','manufacturer_name']);

        foreach ($products as $p) {
            $csv->insertOne([
                $p->product_id,
                $p->product_number,
                $p->upc,
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
