<?php

namespace App\Models\Product;

use App\Models\Master\Variation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationOption extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'variation_id',
        'value',
        'amount',
    ];

    public function variation()
    {
        return $this->hasOne(Variation::class, 'id', 'variation_id');
    }
}
