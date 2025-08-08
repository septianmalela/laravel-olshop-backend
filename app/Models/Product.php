<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'price', 'stock',
                            'sold_qty', 'product_category_id',
                            'image' ];

    public function getImageAttribute($value)
    {
        return $value ? url(Storage::url($value)) : null;
    }

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
