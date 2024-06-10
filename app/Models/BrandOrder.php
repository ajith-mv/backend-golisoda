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
        'commission_percentage',
        'order_status_id'
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

