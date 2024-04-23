<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Session;
use Exception;

class RazorpayPaymentController extends Controller
{
    public function index()
    {        

        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET' );
        // dump( $keyId );
        // dd( $keySecret );
        // $keyId = 'rzp_test_c6EAGa2fqXqKSG';
        // $keySecret = 'AwVXJ0z7rEmnJHoEoV5Ynze2';

        $order_id = 'ORD9090909';
        
        try{

            $api = new Api($keyId, $keySecret);
            $orderData = [
                    'receipt'         => '201200003',
                    'amount'          => 10 * 100, 
                    'currency'        => "INR",
                    'payment_capture' => 1 // auto capture
                ];

            $razorpayOrder = $api->order->create($orderData);
            $razorpayOrderId = $razorpayOrder['id'];
            
            session()->put('razorpay_order_id', $razorpayOrderId);

            $displayAmount          = $amount = $orderData['amount'];
            $displayCurrency        = "INR";
            $data = [
                "key"               => $keyId,
                "amount"            => round($amount),
                "name"              => 'Musee Musical',		
                "image"             =>  "",
                "prefill"           => [
                    "name"              => 'Durairaj',
                    "email"             => "durairaj.pixel@gmail.com",
                    "contact"           => "9551706025",
                    ],
                "notes"             => [
                    "address"           => "",
                    "merchant_order_id" => "ORD201202",
                    ],
                "theme"             => [
                    "color"             => "#F37254"
                    ],
                "order_id"          => $razorpayOrderId,
                
            ];

            $json = json_encode($data);
            $params['json'] = $json;
            return view('platform.payment.razor.view', compact('json', 'data'));
        } catch(Exception $e)
        {
            dd( $e );
        }   
        abort(404); 
    } 


    public function razorpay_response(Request $request)
    { 
        $requestData = $_REQUEST;
        dump( $requestData );
        $keyId = 'rzp_test_c6EAGa2fqXqKSG';
        $keySecret = 'AwVXJ0z7rEmnJHoEoV5Ynze2';
		$success = true;
		$error = "Payment Failed";
		if (empty($_POST['razorpay_payment_id']) === false)
		{
            $razorpay_order_id = session()->get('razorpay_order_id');
            
			$api = new Api($keyId, $keySecret);
		    $finalorder = $api->order->fetch( $razorpay_order_id);
            dump( $finalorder );
			try
			{
			     $attributes = array(
					'razorpay_order_id' => $razorpay_order_id,
					'razorpay_payment_id' => $_POST['razorpay_payment_id'],
					'razorpay_signature' => $_POST['razorpay_signature']
				);

				$api->utility->verifyPaymentSignature($attributes);
			}
			catch(SignatureVerificationError $e)
			{
				$success = false;
				$error = 'Razorpay Error : ' . $e->getMessage();
			}
            dump($success);
            dd( $error );
		} else{	

            if(isset($_POST['error']))
            {
                $orderdata = json_decode($_POST['error']['metadata'],true);
                $_POST['razorpay_payment_id'] = $orderdata['payment_id'];
                $api = new Api($keyId, $keySecret);

                $finalorder = $api->order->fetch( $orderdata['order_id'] );	
                dd( $finalorder );
            }					
		}
       
           
        abort(404);  
	
    }

    public function fail_page(Request $request) {
       dd( 'payment failed' );
    }
}
