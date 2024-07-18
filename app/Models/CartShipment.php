<?php

namespace App\Models;

use App\Models\Master\Brands;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'brand_id',
        'shipping_id',
        'shipping_type',
        'shipping_amount',
        'shiprocket_amount',
        'shiprocket_order_id',
        'shiprocket_shipment_id'
    ];

    /**
     * Get the cart that owns the shipment.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the brand that owns the shipment.
     */
    public function brand()
    {
        return $this->belongsTo(Brands::class);
    }
}
