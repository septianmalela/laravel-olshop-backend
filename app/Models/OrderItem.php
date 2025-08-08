<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'qty', 'product_id', 'order_id'];

    public function product()
    {
        $this->belongsTo(Product::Class)
    }

    public function order()
    {
        $this->belongsTo(Order::Class)
    }
}
