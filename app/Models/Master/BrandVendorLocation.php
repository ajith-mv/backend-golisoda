<?php

namespace App\Models\Master;

use App\Models\Product\Product;
use App\Models\StoreLocator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BrandVendorLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'brand_id',
        'branch_name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'pincode',
        'email_id',
        'mobile_no',
        'contact_person',
        'contact_number',
        'is_default'
    ];
}
