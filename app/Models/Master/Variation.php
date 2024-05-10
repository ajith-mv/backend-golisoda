<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'title',
        'tag_line',
        'sort',
        'value',
        'added_by',
        'status'
    ];
}
