<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDescription extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'product_id',
        'title',
        'description',
        'desc_image',
        'status',
        'order_by',
        'added_by'
    ];
}
