<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreLocatorPincode extends Model
{
    use HasFactory;
    protected $fillable = [

        'store_locator_id',
        'pincode',
        'order_by',
        'added_by',

    ];
}
