<?php

namespace App\Models;

use App\Models\Product\ProductCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMetaTags extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'meta_title',
        'meta_keyword',
        'meta_description'
    ];

    public function product_category()
    {
        return $this->hasOne(ProductCategory::class, 'id', 'category_id');
    }
}
