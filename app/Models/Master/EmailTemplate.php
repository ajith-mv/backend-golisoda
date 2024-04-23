<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'type_id',
        'title',
        'message',
        'params',
        'status',
    ];
}
