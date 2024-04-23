<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pincode extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'pincode',
        'description',
        'shipping_information',
        'added_by',
        'status',
    ];
}
