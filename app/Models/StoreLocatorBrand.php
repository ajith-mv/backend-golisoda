<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreLocatorBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_locator_id',
        'brand_id',
        'status'
    ];
}
