<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProductVariationOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variation_id',
        'variation_option_id',
        'value',
        'amount'
    ];
   
}
