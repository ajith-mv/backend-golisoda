<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductUrl extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'thumbnail_url',
        'video_url',
        'description',
        'order_by',
        'status'
    ];
}
