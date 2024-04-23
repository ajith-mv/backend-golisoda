<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMetaTag extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'meta_title',
        'meta_keyword',
        'meta_description',
    ];
}
