<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use App\Models\GlobalSettings;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use App\Models\Master\OrderStatus;
use PDF;

class UpdateFailedPayments extends Command
{
    protected $signature = 'payments:update-failed';
    protected $description = 'Update failed payment statuses from Razorpay';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        // Fetch failed payments
        $pendingPayments = Order::where('status', 'pending')
            ->where('is_cod', 0)
            ->whereNotNull('payment_response_id')
            ->whereDoesntHave('payments')
            ->get();

        foreach ($pendingPayments as $payment) {
            try {
                $orderId = $payment->payment_response_id;
                $paymentDetails = $api->order->fetch($orderId)->payments();
                foreach ($paymentDetails['items'] as $paymentDetail) {
                    $status = $paymentDetail->status;

                    if ($status == 'captured') {
                       log::info('payment status captured for : '.$payment->id);
                           $order_info = Order::where('order_id', $payment->id)->first();
                        
                            $pay_ins['order_id'] = $order_info->id;
                            $pay_ins['payment_no'] = $paymentDetail['id'];
                            $pay_ins['amount'] = $order_info->amount;
                            $pay_ins['paid_amount'] = number_format($paymentDetail['amount'] / 100, 2, '.', '');
                            $pay_ins['payment_type'] = 'razorpay';
                            $pay_ins['payment_mode'] = 'online';
                            $pay_ins['response'] = serialize($paymentDetails['items']);
                            $pay_ins['status'] = 'paid';
                            Payment::create($pay_ins);

                            $order_status    = OrderStatus::where('status', 'published')->where('order', 3)->first();

                            $order_info->status = 'placed';
                            $order_info->order_status_id = $order_status->id;

                            $order_info->save();

                            $globalInfo = GlobalSettings::first();

                            $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'));
                            Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
                            $this->info("Updated payment ID {$payment->id} to status {$status}");
                    }else{
                        log::info("payment not captured yet for order id: ". $orderId );
                    }
                    break;
                }
                // Update payment status in the database

            } catch (\Exception $e) {
                // Log error if any
                Log::error("Error updating payment ID {$payment->id}: " . $e->getMessage());
            }
        }

        $this->info('Failed payment statuses updated successfully');
    }
}