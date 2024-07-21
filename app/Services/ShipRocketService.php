<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartAddress;
use App\Models\CartProductVariationOption;
use App\Models\CartShipment;
use App\Models\CartShiprocketResponse;
use App\Models\Master\Brands;
use App\Models\Master\BrandVendorLocation;
use App\Models\Master\Customer;
use App\Models\Master\Variation;
use App\Models\Product\Product;
use App\Models\Settings\Tax;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

    public function createOrder($params, $brand_id)
    {
        try {
            $token =  $this->getToken();
            $response =  Shiprocket::order($token)->create($params);
            // dd($response);
            log::info('status code for create order' . $response['status_code']);
            // $response = json_decode($response);
            if (isset($response) && (!empty($response))) {
                if ($response['status_code'] == 1) {

                    CartShiprocketResponse::where('cart_token', $params['order_id'])->delete();
                    $ins_params['cart_token'] = $params['order_id'];
                    $ins_params['rocket_token'] = $token;
                    $ins_params['request_type'] = 'create_order';
                    $ins_params['rocket_order_request_data'] = json_encode($params);
                    $ins_params['rocket_order_response_data'] = $response;
                    $ins_params['order_id'] = $response['order_id'];
                    $ins_params['brand_id'] = $brand_id;

                    CartShiprocketResponse::create($ins_params);
                } else {
                    log::debug($response);
                }
            } else {
                log::debug($e);
                return null;
            }


            return $response;
        } catch (Exception  $e) {
            log::debug($params);
            log::debug($e);
            return null;
        }

        // $success_response = json_decode($response);


    }

    public function updateOrder($params)
    {
        try {
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
            log::debug($params);
            $response = json_decode($response);

            return $response;
        } catch (Exception  $e) {
            log::debug($e);
            log::debug($params);
            return null;
        }
    }

    /**
     * Method getShippingRocketOrderDimensions
     *
     * @param $customer_id 
     * @param $cart_token
     * @param $cart_address_id
     * shipping fee ids : 1- Free Shipping(brandwise free shipping), 2 - Standard Shipping(using shiprocket), 3 - Flat Shipping(by product measuremnet)
     *
     * @return void
     */
    public function getShippingRocketOrderDimensions($customer_id, $cart_token, $cart_address_id)
    {
        $order_id_goli = '';
        log::info($cart_token);
        if (isset($customer_id) && !empty($customer_id)) {
            $shipping_amount = 0;
            $shipping_text = "standard_shipping";
            $is_free = 0;
            $checkCart = Cart::where('customer_id', $customer_id)->orderBy('brand_id', 'asc')->get();
            $customer = Customer::find($customer_id);
            $cartShipAddress = CartAddress::find($cart_address_id);
            $brandIds = [];

            $shipping_amount_db = 0;
            $shippingTypes = [];
            $shipping_name = '';
            if ($cartShipAddress) {
                $product_id = [];
                $cartItemsarr = [];
                $cartTotal = 0;
                $total_weight = 0;
                $delivery_post_code = $cartShipAddress->post_code;
                if (isset($checkCart) && !empty($checkCart)) {
                    foreach ($checkCart as $citems) {

                        if ($citems->products) {
                            CartShipment::where('cart_id', $citems->id)->delete();

                            $pro = $citems->products;
                            $product_id = $pro->id;
                            $variation_title = '';

                            $variation_id = $variation_value = [];
                            $variation_option_id = [];
                            $total_variation_amount = 0;
                            $total_discount_amount = 0;
                            $variationData = CartProductVariationOption::where([['cart_id', $citems->id], ['product_id', $product_id]])->get();
                            if (isset($variationData) && !empty($variationData)) {
                                foreach ($variationData as $variationOptionData) {
                                    $variation_option_id[] = $variationOptionData->variation_option_id;
                                    $variation_id[] = $variationOptionData->variation_id;
                                    $variation_value[] = $variationOptionData->value;

                                    $total_variation_amount = $total_variation_amount + $variationOptionData->amount;
                                    $total_discount_amount = $total_discount_amount + $variationOptionData->discount_amount;
                                }
                                $variations = Variation::whereIn('id', $variation_id)->get();
                                $data = $variation_value;

                                foreach ($variations as $key => $value) {
                                    $variation_title = $value->title . ' : ' . $data[$key];
                                }
                            }
                            $pro_measure = DB::table('product_measurements')
                                ->select("weight")
                                ->where('product_id', $product_id)->first();

                            $total_weight = isset($pro_measure->weight) ? $pro_measure->weight : 1 * $citems->quantity;

                            $tax_total  = 0;
                            $tax = [];
                            $category               = $pro->productCategory;
                            $salePrices             = getProductPrice($pro);
                            $price_with_tax = ($citems->sub_total / $citems->quantity);

                            $tmp = [
                                'hsn' => $pro->hsn_code ?? '',
                                'name' => $pro->product_name . $variation_title,
                                'sku' => $pro->sku . implode('-', $variation_option_id),
                                'tax' => $pro->productCategory->tax->pecentage ?? 12, //$tax_total ?? '',
                                'discount' => '',
                                'units' => $citems->quantity,
                                'selling_price' => $price_with_tax
                            ];

                            // $cartItemsarr[$citems->brand_id][] = $tmp;
                            $cartTotal = $citems->sub_total;

                            $brandIds[] = $citems->brand_id;
                            // Initialize createOrderData for each brand_id
                            if (!isset($createOrderData[$citems->brand_id])) {
                                $createOrderData[$citems->brand_id] = [
                                    'measurement' => [],
                                    'citems' => [],
                                    'cartShipAddress' => $cartShipAddress,
                                    'customer' => $customer,
                                    'cartItemsarr' => [],
                                    'measure' => null,
                                    'cartTotal' => 0,
                                    'total_weight' => 0,
                                    'totalDiscount' => 0
                                ];
                            }
                            $createOrderData[$citems->brand_id]['citems'][] = [
                                'cart_order_no' => $citems->shiprocket_order_number,
                                'id' => $citems->id
                            ];
                            $createOrderData[$citems->brand_id]['cartItemsarr'][] = $tmp;
                            $createOrderData[$citems->brand_id]['cartTotal'] += ($citems->sub_total + $citems->coupon_amount);
                            $createOrderData[$citems->brand_id]['totalDiscount'] += $citems->coupon_amount;
                            $createOrderData[$citems->brand_id]['total_weight'] += $total_weight;

                            $measure = DB::table('product_measurements')
                                ->selectRaw("width, hight, length, weight")
                                ->where('product_id', $product_id)->first();

                            $measure_ment = [
                                "sub_total" => $createOrderData[$citems->brand_id]['cartTotal'],
                                "length" => isset($measure->length) ? $measure->length : 1,
                                "breadth" => isset($measure->width) ? $measure->width : 1,
                                "height" => isset($measure->hight) ? $measure->hight : 1,
                                "weight" => $createOrderData[$citems->brand_id]['total_weight']
                            ];

                            $createOrderData[$citems->brand_id]['measurement'] = $measure_ment;
                        }
                    }
                    $request_params = [];
                    // log::info($createOrderData);
                    if ($brandIds && (!empty($brandIds))) {
                        $uniqueBrandIds = array_unique($brandIds);

                        if (count($uniqueBrandIds) >= 1) {
                            // log::info('different brand ids are in cart');
                            // log::info($uniqueBrandIds);
                            $cart_total = 0;
                            $count = 1;
                            foreach ($uniqueBrandIds as $brandId) {
                                $brand_data = Brands::find($brandId);
                                $branch_data = BrandVendorLocation::where([['brand_id', $brandId], ['is_default', 1]])->first();

                                log::info('brand id' . $brandId);
                                // log::info($createOrderData[$brandId]);
                                // if (isset($brand_data) && ($brand_data->is_free_shipping != 1)) {
                                if (isset($brand_data) && ($brand_data->is_free_shipping == 1)) {
                                    $shipping_amount = 0;
                                    $shipping_text = "free_shipping";
                                    $is_free = 1;
                                }
                                $pickup_post_code = $this->getVendorPostCode($brandId);
                                if (isset($createOrderData[$brandId])) {
                                    $data = $createOrderData[$brandId];

                                    $orderItems = $data['cartItemsarr'];
                                    $cart_total = $data['cartTotal'];
                                    $measure_ment = $data['measurement'];
                                    $brand_name = isset($branch_data) ? $branch_data->branch_name : '';
                                    // $order_id_goli = 'ORD' . $customer_id . $brandId;
                                    $params = $this->getRequestForCreateOrderApi(
                                        $data['citems'],
                                        $data['cartShipAddress'],
                                        $data['customer'],
                                        $orderItems,
                                        $cart_total,
                                        $data['cartTotal'],
                                        $data['total_weight'],
                                        $cart_token,
                                        $brand_name,
                                        $data['totalDiscount'],
                                    );
log::debug($data['citems']);
                                    // $createResponse = $this->createOrder($params);
                                    // Check if order exists in Shiprocket and update it
                                    Log::info("cart token: " . $cart_token);
                                    Log::info("brand_id: " . $brandId);
                                    $existingOrder = CartShiprocketResponse::where('cart_token', $order_id_goli)->where('brand_id', $brandId)->first();
                                    if ($existingOrder) {
                                        log::info('Updating existing order in Shiprocket');
                                        $createResponse = $this->updateOrder($params);
                                        $shiprocket_order_id = isset($createResponse) ? $createResponse->order_id : '';
                                        $shiprocket_shipment_id = isset($createResponse) ? $createResponse->shipment_id : '';
                                        $address_request = $this->getRequestForAddressUpdation($shiprocket_order_id, $data['cartShipAddress'], $customer);
                                        log::debug('Address request');
                                        log::debug($address_request);
                                        $address_update = $this->updateDeliveryAddress($address_request, $shiprocket_order_id);
                                    } else {
                                        log::info('Creating new order in Shiprocket');
                                        $createResponse = $this->createOrder($params, $brandId);
                                        $shiprocket_order_id = isset($createResponse) ? $createResponse['order_id'] : '';
                                        $shiprocket_shipment_id = isset($createResponse->shipment_id) ? $createResponse->shipment_id : '';
                                    }
                                    if (isset($createResponse) && !empty($shiprocket_order_id)) {
                                        log::info('works inside if');

                                        // $shipping_amount = $shipping_amount + $this->getShippingCharges($createResponse['order_id'], $createOrderData[$brandId]['measurement'], $pickup_post_code, $delivery_post_code);
                                        $shiprocket_shipping_charges = $this->getShippingCharges($shiprocket_order_id, $measure_ment, $pickup_post_code, $delivery_post_code);
                                        $shipping_amount = $shipping_amount + $shiprocket_shipping_charges;
                                        if (isset($shiprocket_shipping_charges) && !empty($shiprocket_shipping_charges) && ($shiprocket_shipping_charges != 0)) {
                                            $shipment['shiprocket_amount'] = $shiprocket_shipping_charges;
                                            $shipment['shipping_amount'] = $shiprocket_shipping_charges;
                                            $shipment['shipping_type'] = 'standard_shipping';
                                            $shipment['shipping_id'] = 2;
                                        } else {
                                            $flat_shipping = getVolumeMetricCalculation($data['measurement']['length'], $data['measurement']['breadth'], $data['measurement']['height']);
                                            $shipment['shipping_amount'] = $flat_shipping * 50;
                                            $shipment['shipping_type'] = 'flat_shipping';
                                            $shipment['shipping_id'] = 3;
                                        }
                                        if (isset($brand_data) && ($brand_data->is_free_shipping == 1)) {
                                            $shipment['shipping_amount'] = 0;
                                            $shipment['shipping_type'] = 'free_shipping';
                                            $shipment['shipping_id'] = 1;
                                        }
                                        foreach ($data['citems'] as $citem) {
                                            CartShipment::where('cart_id', $citem['id'])->delete();
                                            $shipment['cart_id'] = $citem['id'];
                                            $shipment['brand_id'] = $brandId;
                                            $shipment['shiprocket_order_id'] = $shiprocket_order_id;
                                            $shipment['shiprocket_shipment_id'] = $shiprocket_shipment_id;
                                            // $shipment['cart_order_no'] = $citem['cart_order_no']; // Include the cart_order_no
                                            CartShipment::create($shipment);
                                        }
                                        // CartShipment::where('cart_id', $data['citems']->id)->delete();
                                        // $shipment['cart_id'] = $data['citems']->id;
                                        // $shipment['brand_id'] = $brandId;
                                        // CartShipment::create($shipment);
                                    } else {
                                        log::info('works inside else');
                                        $flat_shipping = getVolumeMetricCalculation($data['measurement']['length'], $data['measurement']['breadth'], $data['measurement']['height']);
                                        $shipment['shipping_amount'] = $flat_shipping * 50;
                                        $shipment['shipping_type'] = 'flat_shipping';
                                        $shipment['shipping_id'] = 3;
                                        $shipment['shiprocket_amount'] = $flat_shipping * 50;

                                        if (isset($brand_data) && ($brand_data->is_free_shipping == 1)) {
                                            $shipment['shipping_amount'] = 0;
                                            $shipment['shipping_type'] = 'free_shipping';
                                            $shipment['shipping_id'] = 1;
                                        }
                                        foreach ($data['citems'] as $citem) {
                                            CartShipment::where('cart_id', $citem['id'])->delete();
                                            $shipment['cart_id'] = $citem['id'];
                                            $shipment['brand_id'] = $brandId;
                                            // $shipment['cart_order_no'] = $citem['cart_order_no']; // Include the cart_order_no
                                            CartShipment::create($shipment);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $subquery = DB::table('cart_shipments')
                        ->join('carts', function ($join) {
                            $join->on('cart_shipments.cart_id', '=', 'carts.id')
                                ->whereColumn('cart_shipments.brand_id', '=', 'carts.brand_id');
                        })
                        ->where('carts.customer_id', $customer_id)
                        ->select(
                            'cart_shipments.brand_id',
                            'cart_shipments.shipping_type',
                            DB::raw('MAX(gbs_cart_shipments.shipping_amount) as max_shipping_amount')
                        )
                        ->groupBy('cart_shipments.brand_id');

                    // Main query to sum the shipping amounts for each unique brand_id
                    $results = DB::table(DB::raw("({$subquery->toSql()}) as gbs_sub"))
                        ->mergeBindings($subquery)
                        ->select(
                            DB::raw('SUM(gbs_sub.max_shipping_amount) as total_shipment_amount'),
                            'sub.brand_id',
                            'sub.shipping_type'
                        )
                        ->groupBy('sub.brand_id')
                        ->get();

                    // Log::info($results);

                    foreach ($results as $result) {
                        $max_shipping_amount = floatval($result->total_shipment_amount);
                        $shipping_amount_db += $max_shipping_amount;
                        $shippingTypes[] = $result->shipping_type;
                    }

                    // Determine the final shipping type based on the rules provided
                    $shipping_name = $this->determineFinalShippingType($shippingTypes);
                }
            }

            if ($shipping_amount_db < 1) {
                $shipping_name = "free_shipping";
                $is_free = 1;
                $shipping_amount_db = 0;
            }

            log::info('got the shipping amount as' . number_format($shipping_amount_db, 2));
            return ['shipping_title' => ucwords(str_replace('_', ' ', $shipping_name)), 'is_free' => $is_free, 'charges' =>  number_format($shipping_amount_db, 2)];
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
            // log::info(env('SHIPROCKET_CALCULATION') . 'shiprocket calculation');
            if (env('SHIPROCKET_CALCULATION') == 'recommended') {
                $recommended_id = $response['data']['recommended_courier_company_id'];
                // log::info("recommended id is" . $recommended_id);
                foreach ($response['data']['available_courier_companies'] as $company) {
                    if ($company['courier_company_id'] == $recommended_id) {
                        $amount = $company['freight_charge'];
                        // log::info("freight charge is calculated using recommended value: " . $amount);
                    }
                }
            } else {
                $maxRating = -1;
                foreach ($response['data']['available_courier_companies'] as $company) {
                    if ($company['rating'] > $maxRating) {
                        // Update maximum rating and corresponding freight charge
                        $maxRating = $company['rating'];
                        $amount = $company['freight_charge'];
                        // log::info("freight charge is calculated using rating: " . $amount);
                    }
                }
            }
        }

        return $amount;
    }

    public function getVendorPostCode($brand_id)
    {
        $vendor_post_code = env('DEFAULT_VENDOR_POSTCODE');

        $vendor_location_data = BrandVendorLocation::where([['brand_id', $brand_id], ['is_default', 1]])->first();
        if (isset($vendor_location_data) && (!empty($vendor_location_data))) {
            $vendor_post_code = $vendor_location_data->pincode;
        }
        log::info('vendor post code' . $vendor_post_code);
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
    public function getRequestForCreateOrderApi($citems, $cartShipAddress, $customer, $cartItemsarr, $measure, $cartTotal, $total_weight, $cart_token, $brand_name, $total_discount)
    {
        return array(
            "order_id" => $citems->cart_order_no,
            "order_date" => date('Y-m-d h:i'),
            "pickup_location" =>  $brand_name,
            "channel_id" =>  "",
            "comment" =>  "",
            "billing_customer_name" => $cartShipAddress->name,
            "billing_last_name" =>  "",
            "billing_address" =>  $cartShipAddress->address_line1 ?? '',
            "billing_address_2" => $cartShipAddress->address_line2 ?? '',
            "billing_city" => $cartShipAddress->city,
            "billing_pincode" => $cartShipAddress->post_code,
            "billing_state" => $cartShipAddress->state ?? 'Tamil nadu',
            "billing_country" => "India",
            "billing_email" => $cartShipAddress->email ?? $customer->email,
            "billing_phone" => $cartShipAddress->mobile_no,
            "shipping_is_billing" => true,
            "shipping_customer_name" => $cartShipAddress->name,
            "shipping_last_name" => "",
            "shipping_address" => $cartShipAddress->address_line1 ?? '',
            "shipping_address_2" => $cartShipAddress->address_line2 ?? '',
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
            "total_discount" => $total_discount,
            "sub_total" => $cartTotal,
            "length" => isset($measure['length']) ? $measure['length'] : 1,
            "breadth" => isset($measure['width']) ? $measure['width'] : 1,
            "height" => isset($measure['height']) ? $measure['height'] : 1,
            "weight" => $total_weight
        );
    }

    public function getPickupLocation($brand_id)
    {
        $vendor_pickup_location = 'Golisoda';

        $vendor_pickup_location_data = BrandVendorLocation::where([['brand_id', $brand_id], ['is_default', 1]])->first();
        if (isset($vendor_pickup_location_data) && (!empty($vendor_pickup_location_data))) {
            $vendor_pickup_location = $vendor_pickup_location_data->pincode;
        }
        log::info('vendor pickup location' . $vendor_pickup_location);
        return $vendor_pickup_location;
    }

    public function determineFinalShippingType($shippingTypes)
    {
        // Determine the final shipping type based on the rules provided
        if (count(array_unique($shippingTypes)) === 1) {
            // All carts have the same shipping type
            return $shippingTypes[0];
        } else {
            // Different shipping types exist, apply the specified rules
            if (in_array('free_shipping', $shippingTypes) && in_array('standard_shipping', $shippingTypes)) {
                return 'standard_shipping';
            } elseif (in_array('free_shipping', $shippingTypes) && in_array('flat_shipping', $shippingTypes)) {
                return 'flat_shipping';
            } elseif (in_array('flat_shipping', $shippingTypes) && in_array('standard_shipping', $shippingTypes)) {
                return 'standard_shipping';
            }
            // Default fallback (optional based on your needs)
            return null;
        }
    }

    public function getRequestForAddressUpdation($order_id, $shippingAddress, $customer)
    {
        return array(
            "order_id" => $order_id,
            "shipping_customer_name" => $shippingAddress->name,
            "shipping_address" => $shippingAddress->address_line1,
            "shipping_address_2" => isset($shippingAddress->address_line2) ? $shippingAddress->address_line2 : '',
            "shipping_city" => $shippingAddress->city,
            "shipping_pincode" => $shippingAddress->post_code,
            "shipping_country" => "India",
            "shipping_state" => $shippingAddress->state ?? 'Tamil nadu',
            "shipping_email" => $shippingAddress->email ?? $customer->email,
            "shipping_phone" => $shippingAddress->mobile_no,
            "billing_alternate_phone" => $shippingAddress->mobile_no
        );
    }

    public function updateDeliveryAddress($request, $order_id)
    {
        try {
            $token = $this->getToken();
            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post('https://apiv2.shiprocket.in/v1/external/orders/address/update', $request);
            if ($response->successful()) {
                log::info('got the response data');
                // if($response_data){
                log::info('inside response data');
                $ins_params['order_update_request_data'] = json_encode($request);
                $ins_params['order_update_response_data'] = $response;
                $ins_params['request_type'] = 'update_order';

                CartShiprocketResponse::where('order_id', $order_id)->update($ins_params);
                log::info('not worked');
                return $response->json();
            } else {
                return null;
            }
        } catch (Exception $e) {
            log::debug($e);
            log::debug($request);
            return null;
        }
    }

    public function cancelShiprocketOrder($order_ids)
    {
        log::info('works inside cancel order');
        try {
            $token = $this->getToken();
            log::debug('order ids passed for cancel');

            log::debug($order_ids);
            $payload = [
                'ids' => $order_ids
            ];
            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post('https://apiv2.shiprocket.in/v1/external/orders/cancel', $payload);
log::info('debug data for cancel');
                log::debug([
                    'request' => [
                        'url' => $response->effectiveUri(),
                        'headers' => $response->headers(),
                        'body' => $response->body()
                    ],
                    'response' => $response->json()
                ]);
            if ($response->successful()) {
              log::info($response);
                return true;
            } else {
              log::info($response);

                return false;
            }
        } catch (Exception $e) {
            log::debug($e);
            log::debug($order_ids);
            return false;
        }
    }
}
