<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Wishlist;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function addWishlist(Request $request)
    {
        $product_id = $request->product_id;
        $customer_id = $request->customer_id;

        if (!empty($customer_id) && !empty($product_id)) {
            $data = Wishlist::where('customer_id', '=', $customer_id)->where('product_id', '=', $product_id)->first();
            if (empty($data)) {
                $ins['customer_id'] = $customer_id;
                $ins['product_id']  = $product_id;
                $ins['guest_token'] = '';
                $data = Wishlist::create($ins);
                return array('error' => 0, 'message' => 'Wishlist added successfully', 'status' => 'success');
            } else {
                return array('error' => 1, 'message' => 'Wishlist already added', 'status' => 'error');
            }
        }
    }
}
