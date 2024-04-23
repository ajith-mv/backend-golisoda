<?php

namespace App\Models;

use App\Models\Category\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateway extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'gateway_id',
        'access_key',
        'secret_key',
        'merchant_id',
        'working_key',
        'is_primary',
        'mode',
        'added_by'
    ];

    public function userInfo()
    {
        return $this->hasOne(User::class, 'id', 'added_by');
    }

    public function gateway()
    {
        return $this->hasOne(SubCategory::class, 'id', 'gateway_id');
    }


}
