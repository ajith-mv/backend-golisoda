<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingChargePincode extends Model
{
    use HasFactory;

    protected $fillable = [
        'pincode_id',
        'shipping_charge_id',
        'pincode'
    ];
}
