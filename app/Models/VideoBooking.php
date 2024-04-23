<?php

namespace App\Models;

use App\Models\Master\Customer;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoBooking extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'customer_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'reach_type',
        'product_id',
        'preferred_date',
        'preferred_time',
        'status',
    ];

    // public function customer()
    // {
    //     return $this->hasOne(Customer::class, 'id', 'customer_id');
    // }

    // public function product()
    // {
    //     return $this->hasOne(Product::class, 'id', 'product_id');
    // }
}
