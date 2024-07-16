<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartShiprocketResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_token',
        'request_type',
        'rocket_token',
        'order_id',
        'rocket_order_request_data',
        'rocket_order_response_data',
        'shipping_charge_request_data',
        'shipping_charge_response_data',
        'brand_id'
    ];

    public function billingAddress()
    {
        return $this->belongsTo(CartAddress::class, 'cart_token', 'cart_token')->where('address_type', 'billing');
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(CartAddress::class, 'cart_token', 'cart_token')->where('address_type', 'shipping');
    }
}
