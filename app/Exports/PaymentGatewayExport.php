<?php

namespace App\Exports;

use App\Models\PaymentGateway;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PaymentGatewayExport implements FromView
{
    public function view(): View
    {
        $list = PaymentGateway::get();
        return view('platform.exports.global.payment_gateway', compact('list'));
    }
}
