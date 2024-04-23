<?php

namespace App\Models\HomePageSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomepageSetting extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'title',
        'color',
        'homepage_setting_field_id',
        'description',
        'status',
        'order_by',
        'added_by'
    ];

    public function fields()
    {
        return $this->hasOne(HomepageSettingField::class, 'id', 'homepage_setting_field_id');
    }
}
