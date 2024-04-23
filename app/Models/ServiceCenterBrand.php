<?php

namespace App\Models;

use App\Models\Master\Brands;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCenterBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_center_id',
        'brand_id',
        'status'
    ];

    public function brand()
    {
        return $this->hasOne(Brands::class, 'id', 'brand_id');
    }
}
