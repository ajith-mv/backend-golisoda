<?php

namespace App\Exports;

use App\Models\Master\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomerExport implements FromView
{
    public function view(): View
    {
        $list = Customer::select('customers.*')->get();
        return view('platform.exports.customer.excel', compact('list'));
    }
}
