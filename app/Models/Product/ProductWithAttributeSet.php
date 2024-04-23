<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWithAttributeSet extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'product_attribute_set_id',
        'title',
        'tag_line',
        'attribute_values',
        'is_overview',
        'order_by',
        'status',
    ];

    public function attributes()
    {
        return $this->hasOne(ProductAttributeSet::class, 'id', 'product_attribute_set_id');
    }
}
