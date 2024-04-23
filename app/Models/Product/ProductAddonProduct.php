<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAddonProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_addon_id',
        'type',
        'product_id'
    ];
}
