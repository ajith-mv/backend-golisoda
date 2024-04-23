<?php

namespace App\Exports;

use App\Models\Enquiry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EnquiryExport implements FromView
{
    public function view(): View
    {
        $list = Enquiry::all();
        return view('platform.exports.enquiry.excel', compact('list'));
    }
}
