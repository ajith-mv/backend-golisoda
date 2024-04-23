<?php

namespace App\Models\Master;

use App\Models\Category\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'customer_id',
        'address_type_id',
        'name',
        'email',
        'mobile_no',
        'address_line1',
        'address_line2',
        'landmark',
        'countryid',
        'country',
        'post_code',
        'state',
        'stateid',
        'city',
        'is_default',
    ];
    
    public function countries()
    {
        return $this->hasOne(Country::class, 'id', 'countryid');
    }
    public function states()
    {
        return $this->hasOne(State::class, 'id', 'stateid');
    }
    public function city()
    {
        return $this->hasOne(City::class, 'id', 'cityid');
    }
    public function pincode()
    {
        return $this->hasOne(Pincode::class, 'id', 'post_code_id');
    }
    public function PostCode()
    {
        return $this->hasOne(Pincode::class, 'id', 'post_code');
    }
    public function subCategory()
    {
        return $this->hasOne(SubCategory::class, 'id', 'address_type_id');
    }
}
