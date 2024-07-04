<?php

namespace App\Models;

use App\Models\Master\Brands;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'brand_id',
        'order_product_id',
        'qty',
        'price',
        'sub_total',
        'total_excluding_tax',
        'commission_percentage',
        'order_status_id',
        'commission_type',
        'commission_value',
        'shipping_id',
        'shipping_type',
        'shipping_amount',
        'shiprocket_amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brands::class);
    }
}

