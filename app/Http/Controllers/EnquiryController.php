<?php

namespace App\Http\Controllers;

use App\Exports\EnquiryExport;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use Excel;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $title = "Country";
        if ($request->ajax()) {
            $data = Enquiry::select('*');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                   
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('first_name', 'like', "%{$keywords}%")
                                ->orWhere('email', 'like', "%{$keywords}%")
                                ->orWhere('mobile_no', 'like', "%{$keywords}%")
                                ->orWhere('message', 'like', "%{$keywords}%")
                                ->orWhereDate("created_at", $date);
                    }
                })
                ->addIndexColumn()

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
                ->rawColumns([]);
            return $datatables->make(true);
        }
        $breadCrum = array('Contact Us', 'Enquiry');
        $title      = 'Enquiries';
        return view('platform.enquiry.index', compact('breadCrum', 'title'));
    }

    public function export()
    {
        return Excel::download(new EnquiryExport, 'enquiry.xlsx');
    }
}
