<?php

namespace App\Models\Offers;

use App\Models\Product\ProductCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'coupon_id',
        'category_id',
        'quantity',
    ];

    public function category()
    {
        return $this->hasOne(ProductCategory::class, 'id', 'category_id');
    }
    
}
