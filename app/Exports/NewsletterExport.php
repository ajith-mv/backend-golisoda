<?php

namespace App\Exports;

use App\Models\Newsletter;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class NewsletterExport implements FromView
{
    public function view(): View
    {
        $list = Newsletter::all();
        return view('platform.exports.newsletter.newsletter_excel', compact('list'));
    }
}
