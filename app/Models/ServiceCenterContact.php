<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCenterContact extends Model
{
    use HasFactory;
    protected $fillable = [

        'service_center_id',
        'contact',
        'order_by',
        'added_by',

    ];
}
