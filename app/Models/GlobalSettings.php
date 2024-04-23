<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSettings extends Model
{
    use HasFactory;
    protected $fillable = [
        'site_name',
        'site_email',
        'site_mobile_no',
        'favicon',
        'address',
        'logo',
        'copyrights',
        'enable_mail',
        'enable_sms',
        'payment_mode',
        'is_pickup_from_store',
        'is_cod',
        'cod_amount'
    ];

    public function links()
    {
        return $this->hasMany(GlobalSiteLinks::class, 'site_id', 'id');
    }
}
