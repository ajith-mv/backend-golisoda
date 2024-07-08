<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<body>
    <style>
        body {
            border: 1px solid #ddd;
            font-size: 11px;
        }

        /* table td {
            font-size: 10px;
        } */

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
            width: 70% !important;
        }

        .w-50 {
            width: 50%;
        }

        .w-30 {
            width: 30% !important;
        }

        .w-35 {
            width: 35% !important;
        }

        .w-40 {
            width: 40% !important;
        }
        .w-10 {
            width: 10% !important;
        }

        .w-20 {
            width: 20% !important;
        }

        .p-5 {
            padding: 5px;
        }
    </style>
    <h1 style="text-align:center"> Tax Invoice </h1>
    <table class="header-table" cellspacing="0" padding="0">
        <tr>
            <td colspan="2">
                <table class="no-border" style="width: 100%">
                    <tr>
                        <td class="w-70"> <span>
                                <img src="{{ asset('assets/global_setting/logo/1707472536_logo.png') }}" alt=""
                                    height="75"></span> </td>
                        <td class="w-30">
                            <div> Invoice No: </div>
                            <div> Invoice Date: {{ date('d-m-Y')}}</div>
                            <div> Date Range: {{ isset($end_date) ? $end_date : ''}} to {{ isset($start_date) ? $start_date : ''}}</div>

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
                            <div><b> From: </b></div>
                            <h2> {{ $globalInfo->site_name }} </h2>
                            <div> {{ $globalInfo->address }} </div>
                            <div> Phone: {{ $globalInfo->site_mobile_no }} </div>

                            <div> Email: {{ $globalInfo->site_email }} </div>
                            <div> PAN: AAICG7843G</div>
                            <div> GSTIN:33AAICG7843G1ZI</div>

                        </td>

                        <td class="w-35">
                            <div><b> To: </b></div>
                            <div><b>{{ isset($brand_location) ? $brand_location->branch_name : '' }}</b></div>
                            <div>{{ isset($brand_location) ? $brand_location->address_line1 : '' }}</div>
                            <div>{{ isset($brand_location) ? $brand_location->address_line2 : '' }}</div>
                            <div>{{ isset($brand_location) ? $brand_location->city : '' }} ,{{ isset($brand_location) ? $brand_location->state : '' }}</div>
                            <div>{{ isset($brand_location) ? $brand_location->pincode : '' }}</div>

                            <div>{{ isset($brand_location) ? $brand_location->mobile_no : '' }}</div>
                            <div>{{ isset($brand_location) ? $brand_location->email_id : '' }}</div>
                            <div> PAN:</div>
                            <div> GSTIN:</div>
                        </td>


                        <td class="w-30">
                            <div>Seller Billing Details</div>
                            <div>Billing Monthly</div>
                            <div>Payment NEFT Transfer</div>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
    <h1 style="text-align:center"> Invoice Summary </h1>
    <table cellspacing="0" padding="0" class="w-100 item-table">

        <tr>
            <th>Description of Services</th>
            <th>Amount(Rs)</th>
        </tr>
        <tr>
            <td>Total Shipments</td>
            <td>{{ isset($shipment_data->total_shipments) ? $shipment_data->total_shipments : '' }}</td>
        </tr>
        <tr>
            <td>Total Sales (Inclusive Tax)</td>
            <td>{{ isset($data->sale_amount) ? $data->sale_amount : '' }}</td>
        </tr>
        <tr>
            <td> Total Sales (Exclusive Tax)</td>
            <td> {{ isset($data->sale_amount_excluding_tax) ? $data->sale_amount_excluding_tax : '' }} </td>
        </tr>
        <tr>
            <td> Commission(B) @ {{isset($data->com_percentage) ? $data->com_percentage :  '' }} </td>
            <td> {{ isset($data->com_amount) ? number_format($data->com_amount, 2) :  '' }} </td>
        </tr>
        <tr>
            <td> Shipping Charges</td>
            <td> {{ $shipment_data->total_shipping_charge ?? '' }} </td>
        </tr>
        @php
            $commission_amount = $data->com_amount ?? 0;
            $shipping_bared_golisoda = $data->is_shipping_bared_golisoda;
            if($shipping_bared_golisoda){
                $shipping_charge = 0;
            }else{
                $shipping_charge = $shipment_data->total_shipping_charge ?? 0;
            }
            $gst_calculation_amount = $commission_amount + $shipping_charge;
            $cgst_commission = $sgst_commission = $gst_calculation_amount * 0.09;

        @endphp
        <tr>
            <td> CGST on Commission + Shipping (9%) ©</td>
            <td> {{ $cgst_commission }} </td>
        </tr>
        <tr>
            <td> SGST on Commission + Shipping (9%) ©</td>
            <td> {{ $sgst_commission }} </td>
        </tr>
        <tr>
            <td> TDS (1 %)</td>
            <td> {{ isset($data->tds_commission) ? number_format($data->tds_commission, 2) : '' }} </td>
        </tr>
        <tr>
            <td><b>Net Payable Amount</b></td>
            <td><b>{{ 
                (isset($data->sale_amount) ? $data->sale_amount : 0) 
                - (isset($data->com_amount) ? $data->com_amount : 0) 
                - (isset($cgst_commission) ? $cgst_commission : 0) 
                - (isset($sgst_commission) ? $sgst_commission : 0) 
                - (isset($data->tds_commission) ? $data->tds_commission : 0) 
            }}</b></td>
        </tr>
        
    </table>
    <br/>
    <table cellspacing="0" padding="0" class="w-100">
        <tr>
            <td>
                <div><b>Terms & Conditions:</b></div>
                <br/>
                <div>All commissions are calculated based on product Selling prices (inclusive of taxes).</div>
                <div>For any discrepancies or questions regarding this Invoice, please mail us on
                    info@golisodastore.com. Any discrepancy reported after 7 days is not admissible.</div>
                <div>This is a computer generated Invoice and does not require a Signature. </div>
                <div>Cheque/Draft should be drawn in favor of GOLI SODA Sustainable Solutions Private Limited.</div>
            </td>
        </tr>
    </table>
    <br/>
    <h1 style="text-align:center"> Invoice Summary </h1>
    <table cellspacing="0" padding="0" class="w-100 item-table">

        <tr>
            <th class="w-10">S.No</th>
            <th class="w-10">Date</th>
            <th class="w-10">Order Id</th>
            <th class="w-20">Payment Mode</th>
            <th class="w-30">Tracing-Id</th>
            <th class="w-20">Order Amount</th>
        </tr>
        @php
            $count = 1;
        @endphp
        @foreach ($order_info as $order)
            <tr>
                <td>{{ $count }}</td>
                <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                <td>{{ $order->order_no}}</td>
                <td>{{ isset($item->payments) ? $item->payments->payment_type : '' }}</td>
                <td>{{ $order->tracking_id}}</td>
                <td>{{ $order->vendor_order_amount}}</td>
            </tr>
            @php
                $count++;
            @endphp
        @endforeach

    </table>

</body>

</html>
