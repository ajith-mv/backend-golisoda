<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PaymentExport implements FromView
{
    public function view(): View
    {
        $list = Payment::all();
        return view('platform.payment.list._excel', compact('list'));
    }
}
