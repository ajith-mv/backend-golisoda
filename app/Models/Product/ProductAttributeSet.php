<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeSet extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'product_category_id',
        'tag_line',
        'is_searchable',
        'is_comparable',
        'is_use_in_product_listing',
        'status',
        'order_by',
    ];

    public function category()  
    {
        return $this->hasOne(ProductCategory::class, 'id', 'product_category_id');
    }

    public function attributesFields()
    {
        return $this->hasMany(ProductWithAttributeSet::class, 'product_attribute_set_id', 'id')->orderBy('title', 'asc');
    }

    public function attributesFieldsByTitle()
    {
        return $this->hasMany(ProductWithAttributeSet::class, 'product_attribute_set_id', 'id')->orderBy('title', 'asc')->groupBy('title');
    }
}
