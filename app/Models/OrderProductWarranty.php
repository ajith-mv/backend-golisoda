<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductWarranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_product_id',
        'product_id',
        'warranty_id',
        'warranty_name',
        'description',
        'warranty_period',
        'warranty_period_type',
        'warranty_start_date',
        'warranty_end_date',
        'status'
    ];
}
