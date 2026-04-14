<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'price',
        'stock',
        'image',
        'is_available',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
