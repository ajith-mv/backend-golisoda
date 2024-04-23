<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCenterEmail extends Model
{
    use HasFactory;
    protected $fillable = [

        'service_center_id',
        'email',
        'order_by',
        'added_by',

    ];
}
