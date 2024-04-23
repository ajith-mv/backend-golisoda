<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Combo extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'combo_name',
        'slug',
        'tag_line',
        'order_by',
        'status',
        'show_home_page'
    ];
}
