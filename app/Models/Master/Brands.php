<?php

namespace App\Models\Master;

use App\Models\Product\Product;
use App\Models\StoreLocator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Brands extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'brand_name',
        'brand_logo',
        'slug',
        'brand_banner',
        'short_description',
        'notes',
        'order_by',
        'added_by',
        'status',
        'is_top_brand'
    ];

    public function products() {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }

    public function category() {
        return $this->hasMany(Product::class, 'brand_id', 'id')   
                    ->selectRaw('p.*')                 
                    ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
                    ->join( DB::raw('gbs_product_categories as p'), DB::raw('p.id'),'=','product_categories.parent_id')
                    ->groupBy(DB::raw('p.id'));
    }
    public function storeLocator()
    {
        return $this->hasMany(StoreLocator::class,'brand_id','id');
    }
}
