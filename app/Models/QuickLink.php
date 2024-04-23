<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuickLink extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'url',
        'added_by',
        'order_by',
        'status',
    ];
}
