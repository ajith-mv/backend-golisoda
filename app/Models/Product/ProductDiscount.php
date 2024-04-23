<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'discount_type',
        'discount_value',
        'amount'
    ];
}
