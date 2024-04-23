<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'category_name',
        'description',
        'image',
        'slug',
        'order_by',
        'status',
        'added_by',
        'tagline',
        
    ];

    public function subCategory()
    {
        return $this->hasMany(SubCategory::class, 'parent_id', 'id')->where('status', 'published');
    }
}
