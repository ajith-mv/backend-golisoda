<?php

namespace App\Models;

use App\Models\Master\Pincode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_token',
        'customer_id',
        'address_type',
        'name',
        'email',
        'mobile_no',
        'address_line1',
        'address_line2',
        'landmark',
        'country',
        'post_code',
        'state',
        'city'
    ];

    public function PostCode()
    {
        return $this->hasOne(Pincode::class, 'id', 'post_code');
    }
}
