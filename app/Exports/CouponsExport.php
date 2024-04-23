<?php

namespace App\Exports;


use App\Models\Offers\Coupons;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CouponsExport implements FromView
{
    public function view(): View
    {
        $list       = Coupons::select('coupons.*')->where('is_discount_on', 'no')->get();
        return view('platform.exports.coupon.excel', compact('list'));
    }
}
