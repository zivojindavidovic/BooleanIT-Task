<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     description="Category model",
 *     required={"category_id", "category_name", "status"},
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="category_name", type="string", example="Electronics"),
 *     @OA\Property(property="status", type="string", enum={"active", "deleted"}, example="active")
 * )
 */
class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $fillable = [
        'category_name',
        'status'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    public function isDeleted(): bool
    {
        return $this->status == StatusEnum::DELETED->value;
    }

    private function hasActiveProducts(): bool
    {
        return $this->activeProducts() > 0;
    }

    public function offsetGet($offset): mixed
    {
        $camelCaseOffset = lcfirst(str_replace('_', '', ucwords($offset, '_')));

        if (method_exists($this, $camelCaseOffset)) {
            return $this->$camelCaseOffset();
        }

        return parent::offsetGet($offset);
    }

    private function activeProducts(): int
    {
        return $this->products()
            ->where('status', StatusEnum::ACTIVE->value)
            ->count() ?? 0;
    }
}
