<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentView extends Model
{
    use HasFactory;

    protected $fillable = [

        'customer_id',
        'product_id'

    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_id', 'id');
    }
}