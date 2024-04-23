<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Master\Pincode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductAvailCheckController extends Controller
{
  public function index(Request $request)
  {
    $pincode = $request->pin_code; 
    if( !$pincode ) {
      $error = 1;
      $message = 'Pincode is required';
    } else {
      
      $pincode_check = Pincode::select('id', 'shipping_information')->where('status', '1')->where('pincode', $pincode)->first();
      if ($pincode_check) {
        $information = $pincode_check->shipping_information;
        $message = 'Product Shipping avilable in this Pincode';
        $error = 0;
      } else {

        $error = 1;
        $message = 'Product not avilable in this Pincode';   
             
      }
    }

    return array('message' => $message, 'error' => $error, 'information' => $information ?? '');
  }
}
