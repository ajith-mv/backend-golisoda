<?php

namespace App\Models\product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMapAttribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 
        'attribute_id'
    ];

    public function getFilterSpec()
    {
        return $this->hasMany(ProductWithAttributeSet::class, 'product_attribute_set_id', 'id');
    }

    public function attrInfo()
    {
        return $this->hasOne(ProductAttributeSet::class, 'id', 'attribute_id');
    }
}
