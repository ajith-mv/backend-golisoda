<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<body>
    <style>
        body {
            border: 1px solid #ddd;
            font-size: 11px;
        }

        table td {
            font-size: 10px;
        }

        ml-3 {
            margin-left: 3px;
        }

        .header-table,
        .item-table {
            width: 100%;
        }

        .invoice-table td,
        th {
            padding: 0px !important;
            font-weight: bold;
        }

        .header-table td,
        th {
            border: 1px solid #ddd;
            border-collapse: collapse;
            padding: 5px;
        }

        .item-table td,
        .item-table th {
            border: 1px solid #ddd;
            border-collapse: collapse;
            padding: 5px;
        }

        .total-amount-table td,
        .total-amount-table th {
            padding: 5px;
        }

        .no-border td,
        th {
            border: none;
            width: 100%;
            font-size: 12px;
            color: #000000;
        }

        .w-70 {
            width: 70%;
        }

        .w-50 {
            width: 50%;
        }

        .w-30 {
            width: 50%;
        }

        .w-35 {
            width: 35%;
        }

        .w-40 {
            width: 50%;
        }

        .p-5 {
            padding: 5px;
        }
    </style>
    <div style="text-align:center"> SALE ORDER </div>
    <table class="header-table" cellspacing="0" padding="0">
        <tr>
            <td colspan="2">
                <table class="no-border" style="width: 100%">
                    <tr>
                        <td class="w-30"> <span>
                                <img src="{{ asset('assets/global_setting/logo/1707472536_logo.png') }}" alt=""
                                    height="75"></span> </td>
                        <td class="w-70">
                            <h2> {{ $globalInfo->site_name }} </h2>
                            <div> {{ $globalInfo->address }} </div>
                            <!--<div> {{ $globalInfo->site_email }} </div>-->
                            <!--<div> {{ $globalInfo->site_mobile_no }} </div>-->

                        </td>

                    </tr>
                </table>
            </td>

        </tr>
    </table>
    <table class="header-table" cellspacing="0" padding="0">

        <tr>
            <td colspan="2">
                <table class="no-border" style="width: 100%">
                    <tr>
                        <td class="w-35">
                            <div><b> Bill To: </b></div>
                            <div><b>{{ $order_info->billing_name }}</b></div>
                            <div>{{ $order_info->billing_address_line1 }}</div>
                            <div>{{ $order_info->billing_city }}</div>
                            <div>{{ $order_info->billing_state }} - {{ $order_info->billing_pincode->pincode ?? '' }}
                            </div>
                            <div>{{ $order_info->billing_mobile_no }}</div>
                            <div>{{ $order_info->billing_email }}</div>
                        </td>
                        @if (isset($pickup_details) && !empty($pickup_details))
                            <td class="w-35">
                                <div><b> Pickup from Store: </b></div>
                                <div><b>{{ $pickup_details->title }}</b></div>
                                <div>{{ $pickup_details->address }}</div>
                                <div>{{ $pickup_details->group_contacts }}</div>
                                <div>{{ $pickup_details->group_emails }}</div>
                            </td>
                        @else
                            <td class="w-35">
                                <div><b> Ship To: </b></div>
                                <div><b>{{ $order_info->shipping_name }}</b></div>
                                <div>{{ $order_info->shipping_address_line1 }}</div>
                                <div>{{ $order_info->shipping_city }}</div>
                                <div>{{ $order_info->shipping_state }} -
                                    {{ $order_info->shipping_pincode->pincode ?? '' }}</div>
                                <div>{{ $order_info->shipping_mobile_no }}</div>
                                <div>{{ $order_info->shipping_email }}</div>
                            </td>
                        @endif


                        <td class="w-40">

                            <table class="invoice-table w-100">
                                <tr>
                                    <td class="w-50">Invoice No</td>
                                    <td class="w-50">{{ $order_info->order_no }}</td>
                                </tr>
                                <tr>
                                    <td class="w-50">Invoice Date</td>
                                    <td class="w-50">{{ date('d/m/Y', strtotime($order_info->created_at)) }}</td>
                                </tr>
                                <tr>
                                    <td class="w-50">Order No</td>
                                    <td class="w-50">{{ $order_info->order_no }}</td>
                                </tr>
                                <tr>
                                    <td class="w-50">Customer ID</td>
                                    <td class="w-50">{{ $order_info->customer->customer_no }}</td>
                                </tr>
                                <tr>
                                    <td class="w-50"> Payment Status </td>
                                    <td class="w-50"> {{ $order_info->payments->status ?? '' }} </td>
                                </tr>
                                <tr>
                                    <td class="w-50"> Payment Type </td>
                                    <td class="w-50" style=" text-transform: uppercase;">
                                        {{ $order_info->payments->payment_type ?? '' }} </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
    <table class="item-table" cellspacing="0" padding="0">
        <tr>
            <th style="width: 10px;" rowspan="2">S.No</th>
            <th rowspan="2" style="width: 50px;"> ITEM CODE</th>
            <th rowspan="2"> ITEM DESCRIPTION </th>
            <th rowspan="2" style="width: 40px;"> HSN</th>
            <th rowspan="2" style="width: 30px;"> QTY</th>
            <th rowspan="2" style="width: 30px;"> RATE </th>
            {{-- <th rowspan="2" style="width: 40px;"> TAXABLE VALUE </th> --}}
            <th colspan="2" style="width: 100px;"> CGST </th>
            <th colspan="2" style="width: 100px;"> SGST </th>
            <th rowspan="2" style="width: 40px;"> NET Amount </th>
        </tr>
        <tr>
            <th style="width: 40px;">%</th>
            <th style="width: 40px;">Amt</th>
            <th style="width: 40px;">%</th>
            <th style="width: 40px;">Amt</th>
        </tr>
        @if (isset($order_info->orderItems) && !empty($order_info->orderItems))
            @php
                $i = 1;
            @endphp
            @foreach ($order_info->orderItems as $item)
                @php
                    $id = $item->id;
                    $OrderProductVariationOption = App\Models\OrderProductVariationOption::where(
                        'order_product_id',
                        $id,
                    )->get();
                    $variation_id = [];
                    $variation_value = [];
                    foreach ($OrderProductVariationOption as $value) {
                        $variation_id[] = $value->variation_id;
                        $variation_value[] = $value->value;
                    }
                    $variations = App\Models\Master\Variation::whereIn('id', $variation_id)->get();
                @endphp
                <tr>
                    <td>{{ $i }}</td>
                    <td>
                        {{ $item->sku }}
                    </td>
                    <td>
                        <div>
                            {{ $item->product_name }}<br>
                            @php
                                $data = $variation_value;
                            @endphp
                            @foreach ($variations as $key => $value)
                                {{-- @php
                             
                         @endphp --}}
                                {{ $value->title }} : {{ $data[$key] }}<br>
                            @endforeach
                        </div>
                        <div>
                            {{-- Warranty-15-02-2024 --}}
                        </div>
                        <div>
                            {{-- S/R : 12220317926 --}}
                        </div>
                    </td>
                    <td> {{ $item->hsn_code ?? '85044030' }} </td>
                    <td> {{ $item->quantity }} nos</td>
                    @if ($order_info->coupon_amount > 0 && isset($item->coupon_id))
                        <td>{{ number_format($item->strice_price, 2) }}</td>
                    @else
                        <td>{{ number_format($item->price, 2) }}</td>
                    @endif


                    {{-- <td> {{ number_format($item->price, 2) }} </td>
                    <td>{{ number_format($item->price, 2) }}</td> --}}
                    <td>{{ $item->tax_percentage / 2 }}%</td>
                    <td>{{ number_format($item->tax_amount / 2, 2) }}</td>
                    <td>{{ $item->tax_percentage / 2 }}%</td>
                    <td>{{ number_format($item->tax_amount / 2, 2) }}</td>
                    {{-- <td>{{ number_format($item->sub_total, 2) }}</td> --}}
                    @if ($order_info->coupon_amount > 0 && isset($item->coupon_id))
                        <td>{{ number_format($item->strice_price * $item->quantity, 2) }}</td>
                    @else
                        <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                    @endif

                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
        @endif
        @if (isset($order_info->orderAddons) && count($order_info->orderAddons))
            @foreach ($order_info->orderAddons as $item)
                <tr>
                    <td>{{ $i }}</td>
                    <td>
                        {{ $item->addon->title ?? '' }}
                    </td>
                    <td>
                        <div>
                            {{ $item->title }}
                        </div>
                        <div>
                            {{ $item->addon_item_label ?? '' }}
                        </div>
                    </td>
                    <td> </td>
                    <td> 1 no </td>
                    <td> {{ number_format($item->amount, 2) }} </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
        @endif
        <tr>
            <td colspan="7">
                <div>
                    <label for="">Total in words </label>
                </div>
                <div>
                    <b>{{ ucwords(getIndianCurrency($order_info->amount)) }} Only</b>
                </div>
                <div>


                </div>
            </td>
            <td colspan="5" style="text-align:right;width:100%;">
                <table class="w-100 no-border" style="text-align:right">
                    <tr>
                        <td style="text-align: right;">
                            <div>Sub Total </div>
                            <small>(Tax Exclusive)</small>
                        </td>
                        <td class="w-100" style="text-align: right;">
                            <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>
                            {{ number_format($order_info->sub_total, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <!--(%{{ (int) $order_info->tax_percentage }})-->
                        <td style="text-align: right;">Tax </td>
                        <td class="w-100" style="text-align: right;;float:right">
                            <span
                                style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($order_info->tax_amount, 2) }}
                        </td>
                    </tr>
                    @if ($order_info->coupon_amount > 0 && isset($order_info->coupon_code))
                        <tr>
                            <td style="text-align: right;">
                                <div>Coupon Amount </div>
                                @if ($order_info->coupon_type == 'percentage')
                                    <small> ( {{ $order_info->coupon_code }} -
                                        {{ round($order_info->coupon_percentage) }} % )</small>
                                @else
                                    <small>( {{ $order_info->coupon_code }})</small>
                                @endif
                            </td>
                            <td class="w-100" style="text-align: right;"> - <span
                                    style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>
                                {{ number_format($order_info->coupon_amount, 2) }}
                            </td>
                        </tr>
                    @endif

                    @if ($order_info->shipping_amount > 0)
                        <tr>
                            <td style="text-align: right;">
                                <div>Shipping Fee </div>
                                <small>( {{ ucwords(str_replace('_', '', $order_info->shipping_type)) }})</small>
                            </td>
                            <td class="w-100" style="text-align: right;"><span
                                    style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>
                                {{ number_format($order_info->shipping_amount, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if ($order_info->is_cod == 1)
                        <tr>
                            <td style="text-align: right;">
                                <div>COD </div>

                            </td>
                            <td class="w-100" style="text-align: right;"><span
                                    style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($order_info->cod_amount, 2) }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: right;font-weight:700;font-size:14px;">Total</td>
                        <td class="w-100" style="text-align: right;font-weight:700;font-size:14px;">
                            <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>
                            {{ number_format($order_info->amount, 2) }}
                        </td>
                    </tr>

                </table>
            </td>

        </tr>

    </table>

</body>

</html>
