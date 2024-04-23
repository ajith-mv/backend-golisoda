<?php

namespace App\Models\Offers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Brands;
class CouponBrand extends Model
{
    use HasFactory;
    protected $fillable = [
        'coupon_id',
        'brand_id',
        'quantity',
    ];

    public function category()
    {
        return $this->hasOne(Brands::class, 'id', 'brand_id');
    }
}
