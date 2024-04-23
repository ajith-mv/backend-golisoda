<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'weight',
        'weight_type',
        'width',
        'hight',
        'length',
        'dimension_type'
    ];
}
