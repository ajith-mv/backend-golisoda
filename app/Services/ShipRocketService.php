<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartAddress;
use App\Models\CartShiprocketResponse;
use App\Models\Master\Brands;
use App\Models\Master\BrandVendorLocation;
use App\Models\Master\Customer;
use App\Models\Master\Pincode;
use App\Models\Master\State;
use App\Models\MerchantProduct;
use App\Models\Product\Product;
use App\Models\Seller\Merchant;
use App\Models\Seller\MerchantShopsData;
use App\Models\Settings\Tax;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Seshac\Shiprocket\Shiprocket;

class ShipRocketService
{
    public $token;
    public $email;
    public $password;

    public function getToken()
    {
        return Shiprocket::getToken();
    }

    public function rocketToken($order_id)
    {
        $CartShiprocketResponse = CartShiprocketResponse::where('cart_token', $order_id)->first();
        return $this->getToken();
    }

    public function createOrder($params)
    {
        try {
            $token =  $this->getToken();
            $response =  Shiprocket::order($token)->create($params);
            log::info($response['status_code']);
            // $response = json_decode($response);
            if ($response['status_code'] == 1) {

                CartShiprocketResponse::where('cart_token', $params['order_id'])->delete();
                $ins_params['cart_token'] = $params['order_id'];
                $ins_params['rocket_token'] = $token;
                $ins_params['request_type'] = 'create_order';
                $ins_params['rocket_order_request_data'] = json_encode($params);
                $ins_params['rocket_order_response_data'] = $response;
                $ins_params['order_id'] = $response['order_id'];

                CartShiprocketResponse::create($ins_params);
            }else{
                log::debug($response);
            }

            return $response;
        } catch (Exception  $e) {
            log::debug($e);
            return null;
        }

        // $success_response = json_decode($response);


    }

    public function updateOrder($params)
    {
        $token = $this->getToken();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/update/adhoc',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function getShippingRocketOrderDimensions($customer_id, $cart_token, $cart_address_id)
    {
        if (isset($customer_id) && !empty($customer_id)) {
            $shipping_amount = 0;
            $checkCart = Cart::where('customer_id', $customer_id)->get();
            $customer = Customer::find($customer_id);
            $cartShipAddress = CartAddress::find($cart_address_id);

            if ($cartShipAddress) {

                $product_id = [];
                $cartItemsarr = [];
                $cartTotal = 0;
                $total_weight = 0;
                $delivery_post_code = $cartShipAddress->post_code;
                if (isset($checkCart) && !empty($checkCart)) {
                    foreach ($checkCart as $citems) {

                        if ($citems->products) {
                            $pro = $citems->products;

                            $product_id = $pro->id;
                            $pro_measure = DB::table('product_measurements')
                                ->select("weight")
                                ->where('product_id', $product_id)->first();

                            $total_weight = isset($pro_measure->weight) ? $pro_measure->weight : 1 * $citems->quantity;

                            $tax_total  = 0;
                            $tax = [];
                            $category               = $pro->productCategory;
                            $salePrices             = getProductPrice($pro);

                            if (isset($category->parent->tax_id) && !empty($category->parent->tax_id)) {
                                $tax_info = Tax::find($category->parent->tax_id);
                            } else if (isset($category->tax_id) && !empty($category->tax_id)) {
                                $tax_info = Tax::find($category->tax_id);
                            }
                            if (isset($tax_info) && !empty($tax_info)) {
                                $tax = getAmountExclusiveTax($salePrices['price_original'], $tax_info->pecentage);
                                $tax_total =  $tax_total + ($tax['gstAmount'] * $citems->quantity) ?? 0;
                            }
                            $tmp = [
                                'hsn' => $pro->hsn_code ?? null,
                                'name' => $pro->product_name,
                                'sku' => $pro->sku,
                                'tax' => $tax_total ?? '',
                                'discount' => '',
                                'units' => $citems->quantity,
                                'selling_price' => $citems->sub_total
                            ];

                            $cartItemsarr[] = $tmp;
                            $cartTotal = $citems->sub_total;

                            $measure = DB::table('product_measurements')
                                ->selectRaw("width, hight, length, weight")
                                ->where('product_id', $product_id)->first();

                            $measure_ment = [
                                "sub_total" => $cartTotal,
                                "length" => isset($measure->length) ? $measure->length : 1,
                                "breadth" => isset($measure->width) ? $measure->width : 1,
                                "height" => isset($measure->height) ? $measure->height : 1,
                                "weight" => $total_weight
                            ];

                            $brandIds[] = $pro->brand_id;
                            $createOrderData[$pro->brand_id][] = [
                                'measurement' => $measure_ment,
                                'citems' => $citems,
                                'cartShipAddress' => $cartShipAddress,
                                'customer' => $customer,
                                'cartItemsarr' => $cartItemsarr,
                                'measure' => $measure,
                                'cartTotal' => $cartTotal,
                                'total_weight' => $total_weight
                            ];
                        }
                    }
                    $uniqueBrandIds = array_unique($brandIds);

                    if (count($uniqueBrandIds) > 1) {
                        foreach ($uniqueBrandIds as $brandId) {
                            $brand_data = Brands::find($brandId);
                            if (isset($brand_data) && ($brand_data->is_free_shipping == 1)) {
                                $shipping_amount = 0;
                            } else {
                                $pickup_post_code = $this->getVendorPostCode($brandId);
                                $params = $this->getRequestForCreateOrderApi($createOrderData[$brandId]['citems'], $createOrderData[$brandId]['cartShipAddress'], $createOrderData[$brandId]['customer'], $createOrderData[$brandId]['cartItemsarr'], $createOrderData[$brandId]['measure'], $createOrderData[$brandId]['cartTotal'], $createOrderData[$brandId]['total_weight']);
                                $createResponse = $this->createOrder($params);
                                if (isset($createResponse) && !empty($createResponse['order_id'])) {
                                    $shipping_amount = $shipping_amount + $this->getShippingCharges($createResponse['order_id'], $createOrderData[$brandId]['measurement'], $pickup_post_code, $delivery_post_code);
                                }
                            }
                        }
                    } else {
                        $cart_total = 0;
                        $orderItems = [];
                        $brand_data = Brands::find($uniqueBrandIds[0]);
                        if (isset($brand_data) && ($brand_data->is_free_shipping == 1)) {
                            $shipping_amount = 0;
                        } else {
                            $pickup_post_code = $this->getVendorPostCode($uniqueBrandIds[0]);
                            if (isset($createOrderData[$uniqueBrandIds[0]])) {
                                foreach ($createOrderData[$uniqueBrandIds[0]] as $data) {
                                    $orderItems[] = $data['cartItemsarr'];
                                    $cart_total += $data['cartTotal'];
                                    $measure_ment = $data['measurement'];
                                    $params = $this->getRequestForCreateOrderApi($data['citems'], $data['cartShipAddress'], $data['customer'], $orderItems, $cart_total, $data['cartTotal'], $data['total_weight']);
                                }
                                $createResponse = $this->createOrder($params);
                                    if (isset($createResponse) && !empty($createResponse['order_id'])) {
                                        $shipping_amount = $this->getShippingCharges($createResponse['order_id'], $measure_ment, $pickup_post_code, $delivery_post_code);
                                    }
                            } 
                        }
                    }
                }
            }
            return $shipping_amount;
        }
    }

    public function getShippingCharges($order_id, $measure_ment, $pickup_post_code, $delivery_post_code)
    {
        $cart_ship_response = CartShiprocketResponse::where('order_id', $order_id)->first();

        $charge_array = array(
            "pickup_postcode" => $pickup_post_code,
            "delivery_postcode" => $delivery_post_code,

            "order_id" => $order_id,
            "cod" =>  false,
            "weight" => $measure_ment['weight'],
            "length" => $measure_ment['length'],
            "breadth" => $measure_ment['breadth'],
            "height" => $measure_ment['height'],
            "declared_value" => $measure_ment['sub_total'],
            "mode" => "Surface",
            "is_return" => 0,
            "couriers_type" => 0,
            "only_local" => 0
        );

        $token =  $this->getToken();

        $response = Shiprocket::courier($token)->checkServiceability($charge_array);

        $updata = array(
            'shipping_charge_request_data' => json_encode($charge_array),
            'shipping_charge_response_data' => $response
        );
        CartShiprocketResponse::where('order_id', $order_id)->update($updata);
        // $response = json_decode($response);
        $amount = null;
        if (isset($response['data']['available_courier_companies']) && !empty($response['data']['available_courier_companies'])) {
            // log::info($response['data']['available_courier_companies']);
            $recommended_id = $response['data']['recommended_by']['id'];
            log::info("recommended id is" . $recommended_id);
            foreach ($response['data']['available_courier_companies'] as $company) {
                if (isset($response['data']['available_courier_companies'][$recommended_id - 1])) {
                    $amount = $response['data']['available_courier_companies'][$recommended_id - 1]['freight_charge'];
                    log::info("freight charge is: " . $amount);
                    break;
                }
            }
        }

        return $amount;
    }

    public function getVendorPostCode($brand_id)
    {
        $vendor_post_code = '600002';

        $vendor_location_data = BrandVendorLocation::where([['brand_id', $brand_id], ['is_default', 1]])->first();
        if (isset($vendor_location_data) && (!empty($vendor_location_data))) {
            $vendor_post_code = $vendor_location_data->pincode;
        }

        return $vendor_post_code;
    }

    public function getIsFreeShipping($product_id)
    {
        $is_free_shipping = false;
        $product_data = Product::find($product_id);
        if (isset($product_data) && (!empty($product_data))) {
            $brand_id = $product_data->brand_id;
            $brands = Brands::find($brand_id);
            if (isset($brands) && (!empty($brands))) {
                $is_free_shipping = $brands->is_free_shipping;
            }
        }
        return $is_free_shipping;
    }

    public function trackShipment($shipmentId)
    {
        return Shiprocket::track($this->getToken())->throwShipmentId($shipmentId);
    }

    /**
     * Method getRequestForCreateOrderApi - to generate request for createOrder api
     *
     * @param $citems
     * @param $cartShipAddress
     * @param $customer
     * @param $cartItemsarr
     * @param $measure
     * @param $cartTotal
     * @param $total_weight
     *
     * @return array
     */
    public function getRequestForCreateOrderApi($citems, $cartShipAddress, $customer, $cartItemsarr, $measure, $cartTotal, $total_weight)
    {
        return array(
            "order_id" => $citems->cart_order_no,
            "order_date" => date('Y-m-d h:i'),
            "pickup_location" =>  "Golisoda",
            "channel_id" =>  "",
            "comment" =>  "",
            "billing_customer_name" => $cartShipAddress->name,
            "billing_last_name" =>  "",
            "billing_address" =>  $cartShipAddress->address_line1,
            "billing_address_2" => $cartShipAddress->address_line2,
            "billing_city" => $cartShipAddress->city,
            "billing_pincode" => $cartShipAddress->post_code,
            "billing_state" => $cartShipAddress->state ?? 'Tamil nadu',
            "billing_country" => "India",
            "billing_email" => $cartShipAddress->email ?? $customer->email,
            "billing_phone" => $cartShipAddress->mobile_no,
            "shipping_is_billing" => true,
            "shipping_customer_name" => $cartShipAddress->name,
            "shipping_last_name" => "",
            "shipping_address" => $cartShipAddress->address_line1,
            "shipping_address_2" => $cartShipAddress->address_line2,
            "shipping_city" => $cartShipAddress->city,
            "shipping_pincode" => $cartShipAddress->post_code,
            "shipping_country" => "India",
            "shipping_state" => $cartShipAddress->state ?? 'Tamil nadu',
            "shipping_email" => $cartShipAddress->email ?? $customer->email,
            "shipping_phone" => $cartShipAddress->mobile_no,
            "order_items" => $cartItemsarr,
            "payment_method" => "Prepaid",
            "shipping_charges" => 0,
            "giftwrap_charges" => 0,
            "transaction_charges" => 0,
            "total_discount" => 0,
            "sub_total" => $cartTotal,
            "length" => isset($measure->length) ? $measure->length : 1,
            "breadth" => isset($measure->width) ? $measure->width : 1,
            "height" => isset($measure->height) ? $measure->height : 1,
            "weight" => $total_weight
        );
    }
}
