<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'nice_name',
        'iso',
        'iso3',
        'num_code',
        'phone_code',
        'status'
    ];
}
