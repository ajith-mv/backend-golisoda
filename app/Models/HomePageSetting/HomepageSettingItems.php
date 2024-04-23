<?php

namespace App\Models\HomePageSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomepageSettingItems extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'homepage_settings_id',
        'start_size',
        'end_size',
        'setting_image_name',
        'status',
        'order_by',
        'added_by'
    ];
}
