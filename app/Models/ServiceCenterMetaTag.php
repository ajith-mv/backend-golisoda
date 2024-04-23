<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCenterMetaTag extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_center_id',
        'meta_title',
        'meta_keyword',
        'meta_description',
    ];
}
