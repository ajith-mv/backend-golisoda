<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationGroup extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'title',
        'variation_id',
        'category_id',
        'sort',
        'added_by',
        'status'
    ];
}
