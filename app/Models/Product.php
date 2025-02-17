<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Product model",
 *     required={"product_id", "category_id", "department_id", "manufacturer_id", "product_number", "regular_price", "status"},
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=10),
 *     @OA\Property(property="department_id", type="integer", example=3),
 *     @OA\Property(property="manufacturer_id", type="integer", example=5),
 *     @OA\Property(property="product_number", type="string", example="123456"),
 *     @OA\Property(property="sku", type="string", nullable=true, example="987654"),
 *     @OA\Property(property="upc", type="string", nullable=true, example="123456789012"),
 *     @OA\Property(property="regular_price", type="number", format="float", example=999.99),
 *     @OA\Property(property="sale_price", type="number", format="float", nullable=true, example=799.99),
 *     @OA\Property(property="status", type="string", enum={"active", "deleted"}, example="active")
 * )
 */
class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'category_id',
        'department_id',
        'manufacturer_id',
        'product_number',
        'sku',
        'upc',
        'regular_price',
        'sale_price',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id', 'manufacturer_id');
    }

    public function isDeleted(): bool
    {
        return $this->status == StatusEnum::DELETED->value;
    }

    public function offsetGet($offset): mixed
    {
        $camelCaseOffset = lcfirst(str_replace('_', '', ucwords($offset, '_')));

        if (method_exists($this, $camelCaseOffset)) {
            return $this->$camelCaseOffset();
        }

        return parent::offsetGet($offset);
    }
}
