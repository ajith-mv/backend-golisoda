<?php

namespace App\Models;

use App\Models\Master\Customer;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'guest_token',
        'product_id',
        'status'
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class,'id','customer_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
}
