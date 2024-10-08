<?php

namespace App\Listeners;

use App\Events\OrderProcessed;
use App\Models\GlobalSettings;
use App\Services\WatiService;
use App\Models\Master\EmailTemplate;
use PDF;
use Illuminate\Support\Facades\Storage;
use Mail;
use Illuminate\Support\Facades\Log;


class HandleOrderProcessing
{
    protected $watiService;

    public function __construct(WatiService $watiService)
    {
        $this->watiService = $watiService;
    }

    public function handle(OrderProcessed $event)
    {
        $order_info = $event->order_info;
        $pickup_details = $event->pickup_details;
        $variations = $event->variations;
        
        // Generate PDF
        $globalInfo = GlobalSettings::first();
        $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo', 'pickup_details', 'variations'));
        Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());

        // Send Mail
        $emailTemplate = EmailTemplate::select('email_templates.*')
            ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
            ->where('sub_categories.slug', 'new-order')->first();

        $globalInfo = GlobalSettings::first();

        $extract = array(
            'name' => $order_info->billing_name,
            'regards' => $globalInfo->site_name,
            'company_website' => '',
            'company_mobile_no' => $globalInfo->site_mobile_no,
            'company_address' => $globalInfo->address,
            'dynamic_content' => '',
            'order_id' => $order_info->order_no
        );
        $templateMessage = $emailTemplate->message;
        $templateMessage = str_replace("{", "", addslashes($templateMessage));
        $templateMessage = str_replace("}", "", $templateMessage);
        extract($extract);
        eval("\$templateMessage = \"$templateMessage\";");

        $title = $emailTemplate->title;
        $title = str_replace("{", "", addslashes($title));
        $title = str_replace("}", "", $title);
        eval("\$title = \"$title\";");

        $filePath = 'storage/invoice_order/' . $order_info->order_no . '.pdf';
        $send_mail = new \App\Mail\OrderMail($templateMessage, $title, $filePath);
        try {
            $bccEmails = explode(',', env('ORDER_EMAILS'));
            Mail::to($order_info->billing_email)->bcc($bccEmails)->queue($send_mail);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }

        // Send SMS
        $sms_params = array(
            'company_name' => env('APP_NAME'),
            'order_no' => $order_info->order_no,
            'reference_no' => '',
            'mobile_no' => [$order_info->billing_mobile_no]
        );
        sendGBSSms('confirm_order', $sms_params);

        //send whatsapp notification
        $whatsapp_params = [
            ['name' => 'name', 'value' => $order_info->billing_name],
            ['name' => 'order_number', 'value' => $order_info->order_no]
        ];
        $mobile_number = formatPhoneNumber($order_info->billing_mobile_no);
        $this->watiService->sendMessage('917871896064', 'order_placed_message', 'order_placed_message',  $whatsapp_params);

    }
}
