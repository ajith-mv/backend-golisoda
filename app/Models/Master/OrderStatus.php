<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class OrderStatus extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'status_name',
        'description',
        'order',
        'tracking_link',
        'tracking_number',
        'shipping_medium',
        'added_by',
        'status',
    ];
    public function added()
    {
        return $this->hasOne(User::class, 'id', 'added_by');
    }
}
