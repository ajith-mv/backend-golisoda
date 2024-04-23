<?php

namespace App\Models\Product;

use App\Models\ProductAddon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'addon_id',
        'addon_item_id',
        'title',
        'addon_item_label',
        'amount',
        'description',
        'icon'
    ];

    public function addon() {
        return $this->hasOne(ProductAddon::class, 'id', 'addon_id');
    }
}
