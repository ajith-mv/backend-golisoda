<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWarranty extends Model
{
    use HasFactory;

    protected $protected = [
        'product_id',
        'warranty_id',
        'description'
    ];
}
