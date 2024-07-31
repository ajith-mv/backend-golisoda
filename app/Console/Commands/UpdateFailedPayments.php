<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use App\Models\GlobalSettings;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
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
        $failedPayments = Payment::where('status', 'failed')->get();

        foreach ($failedPayments as $payment) {
            try {
                // Fetch payment status from Razorpay
                $paymentDetails = $api->payment->fetch($payment->razorpay_payment_id);
                $status = $paymentDetails->status;

                // Update payment status in the database
                if($payment->status == 'captured'){
                    $payment->status = $status;
                    $payment->save();
                    $order_info = Order::where('order_id', $payment->order_id)->first();
                    $order_info->status = "placed";
                    $order_info->order_status_id = 2;
                    $globalInfo = GlobalSettings::first();
    
                    $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'));
                    Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
                    $this->info("Updated payment ID {$payment->id} to status {$status}");
                }
                
            } catch (\Exception $e) {
                // Log error if any
                Log::error("Error updating payment ID {$payment->id}: " . $e->getMessage());
            }
        }

        $this->info('Failed payment statuses updated successfully');
    }
}
