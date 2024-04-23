<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreLocator extends Model
{
    
    use HasFactory,SoftDeletes;
    protected $fillable = [

        'parent_id',
        'brand_id',
        'title',
        'slug',
        'banner',
        'banner_mb',
        'description',
        'pincode',
        'address',
        'latitude',
        'longitude',
        'email',
        'contact_no',
        'whatsapp_no',
        'map_link',
        'image_360_link',
        'status',
        'added_by',
        'order_by',

    ];
    public function meta()
    {
        return $this->hasOne(StoreLocatorMetaTag::class, 'store_locator_id', 'id');
    }
    public function nearPincodes()
    {
        return $this->hasMany(StoreLocatorPincode::class,'store_locator_id','id');
    }
    public function contacts()
    {
        return $this->hasMany(StoreLocatorContact::class,'store_locator_id','id');
    }
    public function emails()
    {
        return $this->hasMany(StoreLocatorEmail::class,'store_locator_id','id');
    }

    public function brands()
    {
        return $this->hasMany(StoreLocatorBrand::class, 'store_locator_id', 'id')->select('brand_id')->where('status', 'active');
    }

    public function allBrands()
    {
        return $this->hasMany(StoreLocatorBrand::class, 'store_locator_id', 'id');
    }

    public function offers()
    {
        return $this->hasMany(StoreLocatorOffer::class, 'store_locator_id', 'id');
    }
}
