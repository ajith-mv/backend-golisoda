<?php

namespace App\Models\Offers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCustomer extends Model
{
    use HasFactory;
    protected $fillable = [
        'coupon_id',
        'customer_id',
        'quantity',
        
    ];
}
