<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalkThrough extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'title',
        'video_url',
        'file_path',
        'description',
        'type',
        'order_by',
        'status',
        'added_by',
    ];
}
