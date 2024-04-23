<?php

namespace App\Models;

use App\Models\Product\ProductCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponProductCollection extends Model
{
    use HasFactory;
    protected $table = 'coupon_product_collection';
    protected $fillable = [
        'coupon_id',
        'product_collection_id',
        'quantity',
    ];

    public function productCollection()
    {
        return $this->hasOne(ProductCollection::class, 'id', 'product_collection_id');
    }
}
