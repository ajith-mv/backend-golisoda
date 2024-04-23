<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCollectionProduct extends Model
{
    use HasFactory;
    protected $table = 'product_collections_products';
    protected $fillable = [
        'product_collection_id',
        'product_id',
        'order_by'
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id' ); 
    }
    
}
