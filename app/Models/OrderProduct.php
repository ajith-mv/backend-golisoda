<?php

namespace App\Models;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'image',
        'hsn_code' ,
        'sku',
        'quantity',
        'price',
        'strice_price',
        'save_price',
        'base_price',
        'discount_price',
        'tax_amount',
        'tax_percentage',
        'sub_total'
    ];

    public function products()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function chosenVariation(){
        return $this->hasMany(OrderProductVariationOption::class, 'order_product_id', 'id');
    }

    public function vendorTracking(){
        return $this->hasOne(BrandOrder::class, 'order_product_id', 'id');
    }
}
