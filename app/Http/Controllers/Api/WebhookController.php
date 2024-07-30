<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Razorpay\Api\Api;
use App\Models\Payment;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $request = '
        {
    "entity": "event",
    "account_id": "acc_Mmq8vsgXzAkGsX",
    "event": "payment.authorized",
    "contains": [
        "payment"
    ],
    "payload": {
        "payment": {
            "entity": {
                "id": "pay_OZhKQ7kHufsEsr",
                "entity": "payment",
                "amount": 420400,
                "currency": "INR",
                "status": "paid",
                "order_id": "order_OZhK9pRfwriiaT",
                "invoice_id": null,
                "international": false,
                "method": "upi",
                "amount_refunded": 0,
                "refund_status": null,
                "captured": false,
                "description": "Gas Stoves, Table Top Wet Grinder,Juicer Mixer grinders",
                "card_id": null,
                "bank": null,
                "wallet": null,
                "vpa": "test@gmail",
                "email": "shabu.test",
                "contact": "+1234567891",
                "notes": {
                    "address": "test,,nag,Tamil Nadu,India-62915",
                    "merchant_order_id": "VC197206"
                },
                "fee": null,
                "tax": null,
                "error_code": null,
                "error_description": null,
                "error_source": null,
                "error_step": null,
                "error_reason": null,
                "acquirer_data": {
                    "rrn": "305129573624",
                    "upi_transaction_id": "E14ADC8C55DC94ACEEA5AE487C85F82F"
                },
                "created_at": 1721218970,
                "upi": {
                    "vpa": "test@gmail"
                }
            }
        }
    },
    "created_at": 1721218970
}';
$request = json_decode($request, true);
        try {
            $webhookSecret    = config('services.razorpay.webhook_secret');
            // $webhookSignature = $request->header('X-Razorpay-Signature');

            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            // $api->utility->verifyWebhookSignature($request->all(), $webhookSignature, $webhookSecret);
            if (!empty($request['event'])) {
                $payment_event = $request['event'];
                $payment_data = $request['payload']['payment']['entity'];
                if ($payment_event == 'payment.captured') {
                    $payment_id = $payment_data['id'];
                }

                if ($payment_event == 'payment.failed') {
                    $payment_id = $payment_data['id'];
                }

                if ($payment_event == 'payment.authorized') {
                    $payment_id = $payment_data['id'];
                }

                if ($payment_event && $payment_data) {
                    Payment::where('payment_no', $payment_id)->update(['status' => $payment_data['status']]);
                }
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
        return Response::json(true);
    }
}
