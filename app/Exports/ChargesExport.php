<?php

namespace App\Exports;

use App\Models\ShippingCharge;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ChargesExport implements FromView
{
   
    public function view(): View
    {
        $list = ShippingCharge::all();
        // dd($list);
        return view('platform.exports.charges.charges', compact('list'));
    }
}
