<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCenter extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [

        'parent_id',
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
        return $this->hasOne(ServiceCenterMetaTag::class, 'service_center_id', 'id')->select('id','service_center_id','meta_title','meta_keyword','meta_description');
    }
    
    public function parent()
    {
        return $this->belongsTo(ServiceCenter::class, 'parent_id', 'id');
    }
    public function child()
    {
        return $this->hasMany(ServiceCenter::class, 'parent_id', 'id')->select('id','parent_id','title','slug','banner','banner_mb','description','pincode','address','latitude','longitude','email','contact_no','status','order_by');
    }
    public function nearPincodes()
    {
        return $this->hasMany(ServiceCenterPincode::class,'service_center_id','id');
    }
    public function contacts()
    {
        return $this->hasMany(ServiceCenterContact::class,'service_center_id','id');
    }
    public function emails()
    {
        return $this->hasMany(ServiceCenterEmail::class,'service_center_id','id');
    }

    public function brands()
    {
        return $this->hasMany(ServiceCenterBrand::class, 'service_center_id', 'id')->select('brand_id')->where('status', 'active');
    }

    public function allBrands()
    {
        return $this->hasMany(ServiceCenterBrand::class, 'service_center_id', 'id');
    }

    public function offers()
    {
        return $this->hasMany(ServiceCenterOffer::class, 'service_center_id', 'id');
    }
}
