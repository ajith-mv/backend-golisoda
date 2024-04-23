<?php

namespace App\Exports;

use App\Models\SmsTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class SmsExport implements FromView
{
    public function view(): View
    {
        $list = SmsTemplate::select('sms_templates.*','users.name as users_name')->join('users', 'users.id', '=', 'sms_templates.added_by')->get();
        return view('platform.exports.global.sms_template', compact('list'));
    }

}
