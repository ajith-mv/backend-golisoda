<?php

namespace App\Models;

use App\Models\Product\ProductAddonProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAddon extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'title',
        'product_id',
        'description',
        'icon',
        'status',
        'order_by',
        'added_by'
    ];

    public function items()
    {
        return $this->hasMany(ProductAddonItem::class,'product_addon_id','id')->select('id', 'label', 'amount')->where('status','published');
    }

    public function addonProducts() {
        return $this->hasMany(ProductAddonProduct::class, 'product_addon_id', 'id')->where('type','product');
    }
    public function addonCategory() {
        return $this->hasMany(ProductAddonProduct::class, 'product_addon_id', 'id')->where('type','category');
    }

}
