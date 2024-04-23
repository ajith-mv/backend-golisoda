<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'peid_no',
        'tdlt_no',
        'header',
        'template_name',
        'communication_type',
        'template_content',
        'status',
        'added_by',
        'sms_type'
    ];
}
