<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'city',
        'country_id',
        'state_id',
        'pincode_id',
        'description',
        'added_by',
        'status'
    ];
}
