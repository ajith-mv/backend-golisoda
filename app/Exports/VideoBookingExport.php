<?php

namespace App\Exports;


use App\Models\Testimonials;
use App\Models\VideoBooking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class VideoBookingExport implements FromView
{
    public function view(): View
    {
        $list = VideoBooking::select('video_bookings.*', 'customers.first_name as first_name')->join('customers', 'customers.id', '=', 'video_bookings.customer_id')
        // ->join('products', 'products.id', '=', 'video_bookings.product_id')
        ->get();
        return view('platform.exports.video_booking.excel', compact('list'));
    }
}
