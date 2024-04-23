<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonials extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'title',
        'image',
        'short_description',
        'long_description',
        'order_by',
        'status',
        'added_by',
    ];
}
