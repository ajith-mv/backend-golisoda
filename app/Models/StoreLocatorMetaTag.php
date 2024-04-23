<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreLocatorMetaTag extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_locator_id',
        'meta_title',
        'meta_keyword',
        'meta_description',
    ];
}
