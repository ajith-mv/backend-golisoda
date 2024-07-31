<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Razorpay\Api\Api;
use App\Models\GlobalSettings;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Models\Payment;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $request = $request = '
        //         {
        //     "entity": "event",
        //     "account_id": "acc_Mmq8vsgXzAkGsX",
        //     "event": "payment.authorized",
        //     "contains": [
        //         "payment"
        //     ],
        //     "payload": {
        //         "payment": {
        //             "entity": {
        //                 "id": "pay_OZhKQ7kHufsEsr",
        //                 "entity": "payment",
        //                 "amount": 420400,
        //                 "currency": "INR",
        //                 "status": "paid",
        //                 "order_id": "order_OZhK9pRfwriiaT",
        //                 "invoice_id": null,
        //                 "international": false,
        //                 "method": "upi",
        //                 "amount_refunded": 0,
        //                 "refund_status": null,
        //                 "captured": false,
        //                 "description": "Gas Stoves, Table Top Wet Grinder,Juicer Mixer grinders",
        //                 "card_id": null,
        //                 "bank": null,
        //                 "wallet": null,
        //                 "vpa": "test@gmail",
        //                 "email": "shabu.test",
        //                 "contact": "+1234567891",
        //                 "notes": {
        //                     "address": "test,,nag,Tamil Nadu,India-62915",
        //                     "merchant_order_id": "VC197206"
        //                 },
        //                 "fee": null,
        //                 "tax": null,
        //                 "error_code": null,
        //                 "error_description": null,
        //                 "error_source": null,
        //                 "error_step": null,
        //                 "error_reason": null,
        //                 "acquirer_data": {
        //                     "rrn": "305129573624",
        //                     "upi_transaction_id": "E14ADC8C55DC94ACEEA5AE487C85F82F"
        //                 },
        //                 "created_at": 1721218970,
        //                 "upi": {
        //                     "vpa": "test@gmail"
        //                 }
        //             }
        //         }
        //     },
        //     "created_at": 1721218970
        // }';
        // $request = json_decode($request, true);
        // log::info("webhook called");
        try {
            $webhookSecret    = config('services.razorpay.webhook_secret');
            $webhookSignature = $request->header('X-Razorpay-Signature');

            $payload           = $request->getContent();
            $signature         = $request->header('X-Razorpay-Signature');
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            if ($signature == $expectedSignature) {
                if (!empty($request['event'])) {
                    log::info($request['event']);
                    $payment_event = $request['event'];
                    $payment_data = $request['payload']['payment']['entity'];
                    $order_id = $payment_data['order_id'];

                    if ($payment_event == 'payment.failed') {
                        log::info("webhook payment failed");
                        $payment_id = $payment_data['id'];
                        $orderDetails = DB::table('orders')
                            ->join('payments', 'orders.id', '=', 'payments.order_id')
                            ->where('payments.payment_no', $payment_id)
                            ->select('orders.payment_response_id', 'orders.id', 'orders.amount')
                            ->first();
                        if ($orderDetails) {
                            Payment::where('payment_no', $payment_id)->update(['status' => $payment_data['status']]);
                        } else {
                            $order_info = Order::where('payment_response_id', $order_id)->first();
                            $pay_ins['order_id'] = $order_info->id;
                            $pay_ins['payment_no'] = $payment_id;
                            $pay_ins['amount'] = $order_info->amount;
                            $pay_ins['paid_amount'] = number_format($payment_data['amount'] / 100, 2, '.', '');
                            $pay_ins['payment_type'] = 'razorpay';
                            $pay_ins['payment_mode'] = 'online';
                            $pay_ins['response'] = serialize($request['event']);
                            $pay_ins['status'] = $payment_data['status'];
                            $order_status    = OrderStatus::where('status', 'published')->where('order', 3)->first();

                            $order_info->status = 'payment_pending';
                            $order_info->order_status_id = $order_status->id;

                            $order_info->save();
                            Payment::create($pay_ins);
                        }
                    }

                    // if ($payment_event == 'payment.authorized') {
                    //     log::info("webhook payment authorised");
                    //     $payment_id = $payment_data['id'];
                    // }

                    if ($payment_event == 'payment.captured') {
                        $payment_id = $payment_data['id'];
                        log::info("webhook payment captured");
                        $orderDetails = DB::table('orders')
                            ->join('payments', 'orders.id', '=', 'payments.order_id')
                            ->where('payments.payment_no', $payment_id)
                            ->select('orders.payment_response_id', 'orders.id', 'orders.amount')
                            ->first();
                        if ($orderDetails) {
                            Payment::where('payment_no', $payment_id)->update(['status' => $payment_data['status']]);
                            $order_info = Order::where('payment_response_id', $order_id)->first();
                            $order_info->status = "placed";
                            $order_info->order_status_id = 2;
                        } else {
                            $order_info = Order::where('payment_response_id', $order_id)->first();
                            $pay_ins['order_id'] = $order_info->id;
                            $pay_ins['payment_no'] = $payment_id;
                            $pay_ins['amount'] = $order_info->amount;
                            $pay_ins['paid_amount'] = number_format($payment_data['amount'] / 100, 2, '.', '');
                            $pay_ins['payment_type'] = 'razorpay';
                            $pay_ins['payment_mode'] = 'online';
                            $pay_ins['response'] = serialize($request['event']);
                            $pay_ins['status'] = $payment_data['status'];
                            $order_info->status = "placed";
                            $order_info->order_status_id = 2;
                            Payment::create($pay_ins);
                        }

                        $globalInfo = GlobalSettings::first();

                        $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'));
                        Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
                    }


                    if ($payment_event && $payment_data) {
                        log::info("payment status updated for" . $payment_id);
                    }
                }
            } else {
                $errorMessage = 'Signature Mismatch';
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
        return Response::json(true);
    }
}
