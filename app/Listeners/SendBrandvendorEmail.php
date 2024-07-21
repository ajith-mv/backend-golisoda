<?php
namespace App\Listeners;

use App\Events\OrderCreated;
use App\Mail\OrderMail;
use App\Models\BrandOrder;
use App\Models\GlobalSettings;
use App\Models\Master\BrandVendorLocation;
use App\Models\Master\EmailTemplate;
use App\Models\Master\Variation;
use PDF;
use Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendBrandVendorEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(OrderCreated $event)
    {
        $this->sendBrandVendorEmail($event->brandIds, $event->orderId);
    }

    /**
     * Method sendBrandVendorEmail
     *
     * @param $brandIds array
     *
     * @return void
     */
    protected function sendBrandVendorEmail($brandIds, $order_id)
    {
        $uniqueBrandIds =  array_unique($brandIds);
        $globalInfo = GlobalSettings::first();
        if (!empty($uniqueBrandIds)) {
            foreach ($uniqueBrandIds as $singleBrandId) {
                $brandOrderData = BrandOrder::join('orders', 'orders.id', '=', 'brand_orders.order_id')
                    ->where([['brand_id', $singleBrandId], ['order_id', $order_id]])
                    ->get();
                if ($brandOrderData) {
                    $order_info = $brandOrderData[0]->order;
                    $variations = $this->getVariations($order_info);
                    $brand_address = BrandVendorLocation::where([['brand_id', $singleBrandId], ['is_default', 1]])
                        ->join('brands', 'brand_vendor_locations.brand_id', '=', 'brands.id')
                        ->select('brand_vendor_locations.*', 'brands.brand_name')
                        ->first();
                    if (isset($brand_address) && (!empty($brand_address))) {
                        $pdf = PDF::loadView('platform.vendor_invoice.index', compact('brand_address', 'order_info', 'globalInfo', 'variations', 'singleBrandId'));
                        Storage::put('public/invoice_order/' . $brandOrderData[0]->order_id . '/' . $singleBrandId . '/' . $brandOrderData[0]->order->order_no . '.pdf', $pdf->output());
                        $email_slug = 'new-order-vendor';
                        $to_email_address = $brand_address->email_id;
                        $globalInfo = GlobalSettings::first();
                        $filePath = 'storage/invoice_order/' . $brandOrderData[0]->order_id . '/' . $singleBrandId . '/' . $brandOrderData[0]->order->order_no . '.pdf';
                        $extract = array(
                            'name' => $brand_address->brand_name,
                            'regards' => $globalInfo->site_name,
                            'company_website' => '',
                            'company_mobile_no' => $globalInfo->site_mobile_no,
                            'company_address' => $globalInfo->address,
                            'dynamic_content' => '',
                            'order_id' => $order_info->order_no
                        );

                        $this->sendEmailNotificationByArray($email_slug, $extract, $to_email_address, $filePath);
                    }
                }
            }
        }
    }

    /**
     * Method sendEmailNotificationByArray
     *
     * @param $email_slug string
     * @param $extract array
     * @param $to_email_address string
     * @param $filePath string
     *
     * @return void
     */
    public function sendEmailNotificationByArray($email_slug, $extract, $to_email_address, $filePath)
    {
        $emailTemplate = EmailTemplate::select('email_templates.*')
            ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
            ->where('sub_categories.slug', $email_slug)->first();

        $templateMessage = $emailTemplate->message;
        $templateMessage = str_replace("{", "", addslashes($templateMessage));
        $templateMessage = str_replace("}", "", $templateMessage);
        extract($extract);
        eval("\$templateMessage = \"$templateMessage\";");

        $title = $emailTemplate->title;
        $title = str_replace("{", "", addslashes($title));
        $title = str_replace("}", "", $title);
        eval("\$title = \"$title\";");

        // $filePath = 'storage/invoice_order/' . $order_info->order_no . '.pdf';
        $send_mail = new OrderMail($templateMessage, $title, $filePath);
        // return $send_mail->render();
        try {
            $bccEmails = explode(',', env('ORDER_EMAILS'));
            Mail::to($to_email_address)->bcc($bccEmails)->send($send_mail);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    /**
     * Method getVariations
     *
     * @param $order_info object
     *
     * @return array
     */
    public function getVariations($order_info)
    {
        $variation_id = [];
        $variations = [];
        if (isset($order_info->Variation) && !empty($order_info->Variation)) {
            $data = $order_info->Variation;
            foreach ($data as $value) {
                $variation_id[] = $value->variation_id;
            }
            $variations = Variation::whereIn('id', $variation_id)->get();
        }
        return $variations;
    }


}
