<?php

namespace App\Models\Offers;

use App\Models\CouponProductCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupons extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'coupon_name',
        'coupon_code',
        'coupon_sku',
        'start_date',
        'end_date',
        'quantity',
        'used_quantity',
        'calculate_type',
        'calculate_value',
        'minimum_order_value',
        'is_discount_on',
        'coupon_type',
        'from_coupon',
        'is_applied_all',
        'repeated_use_count',
        'is_discount_on',
        'status',
        'order_by',
    ];

    public function couponProducts()
    {
        return $this->hasMany(CouponProduct::class, 'coupon_id', 'id');
    }

    public function couponCustomers()
    {
        return $this->hasMany(CouponCustomer::class, 'coupon_id', 'id');
    }

    public function couponCategory()
    {
        return $this->hasMany(CouponCategory::class, 'coupon_id', 'id');
    }

    public function couponProductCollection()
    {
        return $this->hasMany(CouponProductCollection::class, 'coupon_id', 'id');
    }
    public function couponBrandData()
    {
        return $this->hasMany(CouponBrand::class, 'coupon_id', 'id');
    }


}
