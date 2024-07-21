<?php

namespace App\Models;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    use HasFactory;
    protected $fillable = [
        'customer_id', 
        'guest_token', 
        'product_id', 
        'price', 
        'quantity', 
        'sub_total', 
        'cart_order_no', 
        'coupon_id', 
        'coupon_amount', 
        'shipping_fee_id', 
        'shipping_fee', 
        'is_cod', 
        'cod_amount', 
        'coupon_percentage', 
        'coupon_code', 
        'coupon_type',
        'cart_id',
        'brand_id',
        'shiprocket_order_number',
        'base_unique_id',
        'suffix'
    ];

    public function products()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function addons()
    {
        return $this->hasMany(CartProductAddon::class, 'cart_id', 'id');
    }

    public function variationOptions()
    {
        return $this->hasMany(CartProductVariationOption::class, 'cart_id', 'id');
    }

    public function rocketResponse()
    {
        return $this->hasOne(CartShiprocketResponse::class, 'cart_token', 'shiprocket_order_number');
    }

    public function shipments()
    {
        return $this->hasMany(CartShipment::class, 'cart_id');
    }
}
