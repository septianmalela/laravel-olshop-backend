<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image'];

    public function getImageAttribute($value)
    {
        return $value ? url(Storage::url($value)) : null;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
