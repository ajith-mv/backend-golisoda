<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\GlobalSettings;
use App\Models\Master\Brands;
use App\Models\Master\EmailTemplate;
use App\Models\Order;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductDescription;
use App\Models\ServiceCenter;
use App\Models\SmsTemplate;
use App\Models\StoreLocator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PDF;
use Mail;
use Razorpay\Api\Api;
use Exception;
use Illuminate\Support\Str;
class TestController extends Controller
{

    public function index(Request $request)
    {

        // $number = ['919551706025'];
        // $name   = 'Durairaj';
        // $orderId = 'IOP9090909P';
        // $companyName = 'Musee Musical';
        // $credentials = 'durairamyb@mail.com/09876543456';
        // $message = "Dear $name, Ref.Id $orderId, Thank you for register with $companyName. Your credentials are $credentials. -MUSEE MUSICAL";
        // sendSMS($number, $message, []);
        // $all_category = ProductCategory::where('status', 'published')->where('parent_id', 0)->get();

        // $category = [];
        // if (isset($all_category) && !empty($all_category)) {
        //     foreach ($all_category as $cat_item) {

        //         // dump( $cat_item->childCategory );
        //         if (isset($cat_item->childCategory) && !empty($cat_item->childCategory)) {
        //             foreach ($cat_item->childCategory as $sub_item) {
        //                 // dump( $sub_item );
        //                 // dump( count($sub_item->products ) );
        //                 if (!isset($category[$cat_item->id])) {
        //                     $category[$cat_item->id] = array('id' => $cat_item->id, 'name' => $cat_item->name, 'slug' => $cat_item->slug);
        //                 }
        //                 if (count($sub_item->products) > 0) {

        //                     $category[$cat_item->id]['child'][] = array('id' => $sub_item->id, 'name' => $sub_item->name, 'slug' => $sub_item->slug);
        //                 }
        //             }
        //         }
        //     }
        // }
        // $new_menu = [];
        // if (!empty($category)) {
        //     foreach ($category as $key => $value) {

        //         $new_menu[] = $value;
        //     }
        // }



        // // $response = Http::post('https://apiv2.shiprocket.in/v1/external/auth/login',[
        // //     'header' => 'Content-Type: application/json',
        // //     'email' => 'duraibytes@gmail.com',
        // //     'password' => 'Pixel@2022'
        // // ]);

        // dd( $response );
        $products=Product::get();
        foreach($products as $product){
            $po=Product::find($product->id);
            $po->product_url = Str::slug($product->product_name);
            $po->update();
            
        }
        dd(1);

    }

    public function sendSms($sms_type, $details = [])
    {
        $info = SmsTemplate::where('sms_type', $sms_type)->first();
        if (isset($info) && !empty($info)) {

            $number = ['919551706025'];
            $details = array(
                'name' => 'durairja',
                'reference_id' => '88978979',
                'company_name' => env('APP_NAME'),
                'login_details' => 'loginId:durairamyb@mail.com,password:09876543456',
                'mobile_no' => ['919551706025']
            );
            // $name   = 'Durairaj';
            // $reference_id = 'ORD2015';
            // $company_name = $info->company_name;
            // $credential = 'email/password';
            // $subscribtion_id = '#SUB2022';
            // $rupees = 'RS250000';
            // $payment_method = 'online razorpay';
            // $first_name  = 'Durai';
            // $last_name  = 'raj';
            // $order_no = 'ORD2013';
            // $company_url  = 'https://www.onlinemuseemusical.com/';
            // $latest_update = 'Latest Updates';
            // $tracking_no = '#um89898990000009';
            // $tracking_url = 'https://www.onlinemuseemusical.com/';

            $templateMessage = $info->template_content;
            $templateMessage = str_replace("{", "", addslashes($templateMessage));
            $templateMessage = str_replace("}", "", $templateMessage);

            extract($details);

            eval("\$templateMessage = \"$templateMessage\";");

            $params = array(
                'entityid' => $info->peid_no,
                'tempid' => $info->tdlt_no,
                'sid'   => urlencode(current(explode(",", $info->header)))
            );

            sendSMS($number, $templateMessage, $params);
        }
    }

      public function invoiceSample(Request $request)
    {
        $info = 'teste';
 
        if( $request->id ) {
            // selectRaw('gbs_orders.*, pay.order_id,pay.payment_no,pay.status as payment_status')->join(DB::raw('(select o.created_at,o.order_id, o.payment_no, o.status from gbs_payments o WHERE o.created_at =( SELECT MAX(gbs_payments.created_at) FROM gbs_payments WHERE order_id = o.order_id ) ) as pay'), function ($join){
            //     $join->on(DB::raw('pay.order_id'), '=', 'orders.id');
            // })->
            $order_info = Order::where('id', $request->id)->first();
            $globalInfo = GlobalSettings::first();
            $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'));    
            Storage::put('public/invoice_order/'.$order_info->order_no.'.pdf', $pdf->output());
            $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'))->setPaper('a4', 'portrait');
            return $pdf->stream('test.pdf');
        }
    }

    public function sendMail()
    {

        Mail::send([], [], function ($message) {
            $message->to('durairaj.pixel@gmail.com', 'Tutorials Point')
                ->subject('subject')
                ->setBody('some body on the mail', 'text/html');
        });
        return response()->json([
            'message' => 'Email has been sent.'
        ]);
        die;
        $email = 'durairaj.pixel@gmail.com';

        $mailData = [
            'title' => 'Demo Email',
            'url' => 'https://www.positronx.io'
        ];
        $data = 'Durairaj mail is testing';

        Mail::to($email)->send(new TestMail($data, 'Test Mail From Durariaj'));

        return response()->json([
            'message' => 'Email has been sent.'
        ]);

        // $emailTemplate = EmailTemplate::select('email_templates.*')
        //                         ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
        //                         ->where('sub_categories.slug', 'new-registration')->first();

        // $globalInfo = GlobalSettings::first();


        // $extract = array(
        //                 'name' => 'Durairaj', 
        //                 'regards' => $globalInfo->site_name, 
        //                 'company_website' => '',
        //                 'company_mobile_no' => $globalInfo->site_mobile_no,
        //                 'company_address' => $globalInfo->address 
        //             );
        // $templateMessage = $emailTemplate->message;
        // $templateMessage = str_replace("{","",addslashes($templateMessage));
        // $templateMessage = str_replace("}","",$templateMessage);
        // extract($extract);
        // eval("\$templateMessage = \"$templateMessage\";");

        // $body = [
        //     'content' => $templateMessage,
        //     'title' => $emailTemplate->title
        // ];
        // $send_mail = new TestMail($templateMessage, $emailTemplate->title);
        // // return $send_mail->render();
        // Mail::to("durairaj.pixel@gmail.com")->send($send_mail);

    }

    public function payment()
    {

        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET');

        $order_id = 'ORDE890980';
        $pay_amount = 1;

        $shipping_address = array(
            'name' => 'durairaj',
            'email' => 'durairaj.pixel@gmail.com',
            'mobile_no' => '9551706025'
        );
        try {

            $api = new Api($keyId, $keySecret);
            $orderData = [
                'receipt'         => $order_id,
                'amount'          => $pay_amount * 100,
                'currency'        => "INR",
                'payment_capture' => 1 // auto capture
            ];

            $razorpayOrder = $api->order->create($orderData);
            $razorpayOrderId = $razorpayOrder['id'];

            $amount = $orderData['amount'];
            $displayCurrency        = "INR";
            $data = [
                "key"               => $keyId,
                "amount"            => round($amount),
                "currency"          => "INR",
                "name"              => 'GBS ',
                "image"             => asset(gSetting('logo')),
                "description"       => "Secure Payment",
                "prefill"           => [
                    "name"              => $shipping_address['name'],
                    "email"             => $shipping_address['email'],
                    "contact"           => $shipping_address['mobile_no'],
                ],
                "notes"             => [
                    "address"           => "",
                    "merchant_order_id" => $order_id,
                ],
                "theme"             => [
                    "color"             => "#F37254"
                ],
                "order_id"          => $razorpayOrderId,
            ];

            return view('test.razor_pay', ['data' => $data]);
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function verifySignature(Request $request)
    {

        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET');

        $customer_id = $request->customer_id;
        dd($request);
        $razor_response = $request->razor_response;
        $status = $request->status;

        $success = true;
        $error_message = "Payment Success";

        if (isset($razor_response['razorpay_payment_id']) && empty($razor_response['razorpay_payment_id']) === false) {
            $razorpay_order_id = $razor_response['razorpay_order_id'];
            $razorpay_signature = $razor_response['razorpay_signature'];
            // $razorpay_order_id = session()->get('razorpay_order_id');

            $api = new Api($keyId, $keySecret);
            $finalorder = $api->order->fetch($razorpay_order_id);

            try {
                $attributes = array(
                    'razorpay_order_id' => $razorpay_order_id,
                    'razorpay_payment_id' => $razor_response['razorpay_payment_id'],
                    'razorpay_signature' => $razor_response['razorpay_signature']
                );

                $api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
                $error_message = 'Razorpay Error : ' . $e->getMessage();
            }
        } else {
            $success = false;
            $error_message = 'Payment Failed';

            if (isset($request->razor_response['error']) && !empty($request->razor_response['error'])) {

                $orderdata = $request->razor_response['error']['metadata'];
                $razorpay_payment_id = $orderdata['payment_id'];
                $razorpay_order_id = $orderdata['order_id'];
                dump($request);
                $api = new Api($keyId, $keySecret);

                $finalorder = $api->order->fetch($orderdata['order_id']);
                dd($finalorder);
            }
        }

        return  array('success' => $success, 'message' => $error_message);
    }

    function testPaymentStatusTracker()
    {
        $working_key = 'B00B81683DCD0816F8F32551E2C2910B';
        $response = '3601f58b98fb48e3e36112be0bc24a780a5feef78ebb91c949fcb1b2eb6d09f9b62714ef5839d83137f8c1903ae1dadd162e7fb7a3640ba73ee298108973a377cf62a02441024851976ba5e86574131d6fd17ac2c721bab703338e00da3acce67649f6101f14d4f0cf7ac36d169ca0e81fe2685824603baf9a8dd364a2733902d0fa568197e8b6a2d372d5cc8ec911a70d289bb9d0dc8def29152c9c81b676ee971de00f1f16c147d2e624b6b837b07ea7e7d481ceb001476cebe0d076bb2284b182ee622f98520ddbcc61f2842e4fc5c2383eb7fe5d229dd2a41de51fbe74390d40a409d3a5b4ad574473d591c1c3a120f09a062e5438f600672400c4d7e91b46889e813059bd7cffe4760b023a051d769569330c8d62889553f17efaf3eb3a03c0ac11947f62bbaf30a622ea80a70aef6d575892ff7441635b86ab98541b31576e23e6382ed59472808ec796026d655a2247c3dba007a81f3cd0b0b978df38011008ff2b17fec354b2d28d54fbd217b3b96ed9bcdfd678994957fd9d8684113a0cca67e12f6833c7468745a1adf0fa6b4920c7365b9a1fbf70a0d8df470729e7f5a7cd22ca8a22aec443f2cb8468739a6a94e46aed5bd5a7e684c2cd86e1f79d0ef070bca6494fdd8f2f3c0aed791acc5626244acd719828b95cb0a52bbf9e4abb9d0ffb77d7d29a1767189992272e961334fb304f09b17d877fd006085a71810a9b40f9bd1e8e69efd50c7b63470a1fd76549b12d2c42ae5b81234b1ae343435636b840108171a041b4c82cd1b412941d2fc9a9e6143c4a61120101359db1d1bc592d0b9c429f269fe6953d6b0ebb048516c99b5b3d2982fec77c842716719248cc8f1aa7d705d04d4265032cc38f3028a22c48ee9b715270c5b0dcf0e05b373ef645b8bb6b2b09886b3f17dacba19ed13933854266e5cdfe3e7374fc3efe6acb081b28f249214c31931f42a59a14b62cd1de8c686165f862ff9b10546945668a2c0de35d4807382d534c6456afc3c50b49138c24ff22631c9c99a22b47925250fe6573871def58e698768439994b4d9c8e6e39a0250ed39c1a526d5d11aeb6d4752fb73f4ac58f1ed4a4856d13aaeb6e4b112c230d18b9aa292dfcf433ea04fce3d9836b54a0dcdd7fc67847261b13fccdfeb4f5bdf746daacb3d302c59c6f7547d4efb3e1bfa4abcceaf202f7671d22df739a9944a5f6fb38ef6dc99043c6d751f7d15a8c7529b18eac48797ea8c9fb386488edcf7ef498d1c28ca7b9d1f9a0eb04d13a382c175cd6a219e7583ea66d0e4762c3af5537c594c3710c3f5ccdd87d69bf63869659672ffe963792b395d088d7a4387bf507e36e5585d4e200ca44c7a9f13d60448d380114959805bd9b2cd42b4beac97086cac0cac858c4dcc6ef4132069653273c3790ffcf1bdf407b565d5241da6f83b3c021a98b848b881ce197579c5917170d7af157ae3bf05f5ddd50d25fd14b3779ccbbb512f48947719e739722f9d01ce1d68fe1f5988745cad9c8b8170b32608276ae56b8aec615221eb5e52612c2f67d6fcac2e2068f88b7e00b6b2ebf26427829057f9aadf7ba8cee9ed530bc6ddc7108f8252bf646a2d9a7930f84900d1c2b427b190ecf6c284591f80a59cca4531a94a83dd5546d8e103db459ff7f43b66da86642a57b962fd2dc3a419862be09de277d0c1fd6c205f095b07d6e83018dad3972d7acffd4220ca9d36b817c9b1937a145f39b171619aa8a2e873289ff0e1a6b70fbed2503d79faa65ca38673d25f49c09f4bfdcde4d86386a3d870236f7630c842e91b7bba263c07aa8b9982b86801ae5bfe8ddbebcd80d590a8330d20267ead354843f10e9ddc22dd8c8d8bcfe7464d7f0573cdcac';

        $return = $this->statusDecrypt(trim($response), $working_key);
        dump($return);
        dd(json_decode($return));
    }

    function statusDecrypt($encryptedText, $key)
    {
        $key = $this->statusHextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = $this->statusHextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }

    function status_pkcs5_pad($plainText, $blockSize)
    {
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }

    //********** Hexadecimal to Binary function for php 4.0 version ********

    function statusHextobin($hexString)
    {
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length) {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0) {
                $binString = $packedString;
            } else {
                $binString .= $packedString;
            }

            $count += 2;
        }
        return $binString;
    }

    public function testDescription()
    {
        $deleted_data = DB::select("select * from  gbs_product_descriptions  
        WHERE 
        deleted_at is not null 
        and (desc_image is not null and desc_image != '' )
        GROUP by title
        order by deleted_at DESC");

        if (isset($deleted_data) && !empty($deleted_data)) {
            foreach ($deleted_data as $items) {
                $update_data = ProductDescription::where(['product_id' => $items->product_id, 'title' => $items->title])
                    ->whereNull('deleted_at')
                    ->where(function ($query) {
                        $query->whereNotNull('desc_image');
                        $query->orWhere('desc_image', '!=', '');
                    })->first();

                if ($update_data) {
                    $update_data->desc_image = $items->desc_image;
                    $update_data->save();
                }

                dump($update_data);
            }
        }
    }

    public function generateSiteMap(Request $request)
    {
        $products = Product::all();
        $pages = array(
            'https://golisodastore.com',
            // 'https://golisodastore.com/about-us',
            'https://golisodastore.com/shipping-delivery',
            'https://golisodastore.com/terms-conditions',
            'https://golisodastore.com/privacy-policy',
            'https://golisodastore.com/products',
            'https://golisodastore.com/my-account/profile',
            'https://golisodastore.com/cart'
        );
        $store_locator = StoreLocator::all();
        $store_center = ServiceCenter::all();
        return response()->view('site-map', compact('products', 'store_locator', 'pages', 'store_center'))->header('Content-Type', 'text/xml');
    }

    public function testMailcontent()
    {
        $emailTemplate = EmailTemplate::select('email_templates.*')
            ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
            ->where('sub_categories.slug', 'new-order')->first();
        $globalInfo = GlobalSettings::first();
        $order_info = Order::find(5);
        $extract = array(
            'name' => $order_info->billing_name,
            'regards' => $globalInfo->site_name,
            'company_website' => '',
            'company_mobile_no' => $globalInfo->site_mobile_no,
            'company_address' => $globalInfo->address,
            'dynamic_content' => '',
            'order_id' => $order_info->order_no
        );
        extract($extract);

        $templateMessage = $emailTemplate->message;
        $templateMessage = str_replace("{", "", addslashes($templateMessage));
        $templateMessage = str_replace("}", "", $templateMessage);
        eval("\$templateMessage = \"$templateMessage\";");

        $title = $emailTemplate->title;
        $title = str_replace("{", "", addslashes($title));
        $title = str_replace("}", "", $title);
        eval("\$title = \"$title\";");
        dd( $templateMessage );
        $filePath = 'storage/invoice_order/' . $order_info->order_no . '.pdf';
        $send_mail = new OrderMail($templateMessage, $title, $filePath);
        // return $send_mail->render();
        $bccEmails = explode(',', env('BCC_EMAILS'));
        Mail::to($order_info->billing_email)->bcc($bccEmails)->send($send_mail);
    }
}
