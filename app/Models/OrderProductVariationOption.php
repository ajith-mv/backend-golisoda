<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductVariationOption extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'order_product_id',
        'variation_id',
        'variation_option_id',
        'value',
        'amount',
        'discount_amount'
    ];
}
