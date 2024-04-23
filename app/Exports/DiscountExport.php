<?php

namespace App\Exports;

use App\Models\Offers\Coupons;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DiscountExport implements FromView
{
    public function view(): View
    {
        $list       = Coupons::select('coupons.*')->where('is_discount_on', 'yes')->get();
        return view('platform.exports.coupon.discount_excel', compact('list'));
    }
}
