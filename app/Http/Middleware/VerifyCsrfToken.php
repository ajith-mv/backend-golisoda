<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    
    protected $except = [
        'razorpay.payment', 'ccavenue.response', 'ccavenue.request', 'ccpayment',
        'api/*'
    ];

}
