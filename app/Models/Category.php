<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;

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

    public function activeProducts(): int
    {
        return $this->products()
            ->where('status', StatusEnum::ACTIVE->value)
            ->count() ?? 0;
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
