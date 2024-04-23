<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerReviewController extends Controller
{
    public function addReviews(Request $request)
    {
        $customer_id = $request->customer_id;
        $product_id = $request->product_id;
        $star = $request->star;
        $comments = $request->comments;

        $validator      = Validator::make($request->all(), [
            'product_id' => 'required',
            'star' => 'required',
            'comments' => 'required',
        ]);

        if ($validator->passes()) {
            $error = 0;
            $message = 'Review has been sent for approval to show';

            $ins['customer_id'] = $customer_id;
            $ins['product_id'] = $product_id;
            $ins['star'] = $star;
            $ins['comments'] = $comments;

            Review::updateOrCreate(['product_id' => $product_id, 'customer_id' => $customer_id], $ins);

        } else {
            $error = 1;
            $message = $validator->errors()->all();
        }
        return array('error' => $error, 'message' => $message);
    }

    public function listReviews(Request $request)
    {
        $take = $request->take ?? 4;
        
        $total = Review::where('product_id', $request->product_id)
                ->count();
        $data = Review::select('id', 'comments', 'star', 'created_at', 'customer_id', 'product_id')->with(['customer', 'product'])->where('product_id', $request->product_id)
                ->skip(0)->take($take)
                ->get();

        
        return array('total' => $total, 'reviews' => $data);
    }
}
