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
                            <div> Invoice Date: </div>
                            <div> Date Range: </div>

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
                            <div><b>{{ $brand_location->branch_name }}</b></div>
                            <div>{{ $brand_location->address_line1 }}</div>
                            <div>{{ $brand_location->address_line2 }}</div>
                            <div>{{ $brand_location->city }} ,{{ $brand_location->state }}</div>
                            <div>{{ $brand_location->pincode }}</div>

                            <div>{{ $brand_location->mobile_no }}</div>
                            <div>{{ $brand_location->email_id }}</div>
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
            <td>{{ $data->total_shipments }}</td>
        </tr>
        <tr>
            <td>Total Sales (Inclusive Tax)</td>
            <td>{{ $data->sale_amount }}</td>
        </tr>
        <tr>
            <td> Total Sales (Exclusive Tax)</td>
            <td> {{ $data->sale_amount_excluding_tax }} </td>
        </tr>
        <tr>
            <td> Commission(B) @ 20% </td>
            <td> {{ $data->com_percentage ?? '' }} </td>
        </tr>
        <tr>
            <td> Shipping Charges</td>
            <td> {{ $data->total_shipments ?? '' }} </td>
        </tr>
        <tr>
            <td> CGST on Commission + Shipping (9%) ©</td>
            <td> {{ $data->com_amount ?? '' }} </td>
        </tr>
        <tr>
            <td> SGST on Commission + Shipping (9%) ©</td>
            <td> {{ $data->com_amount ?? '' }} </td>
        </tr>
        <tr>
            <td> TDS (1 %)(D)</td>
            <td> {{ $data->com_amount ?? '' }} </td>
        </tr>
        <tr>
            <td> <b>Net Payable Amount</b> </td>
            <td> <b>{{ $data->com_amount ?? '' }}</b> </td>
        </tr>
    </table>
    <br/>
    <table cellspacing="0" padding="0" class="w-100">
        <tr>
            <td>
                <div><b>Terms & Conditions:</b></div>
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
            <th>S.No</th>
            <th>Date</th>
            <th>Order Id</th>
            <th>Payment Mode</th>
            <th>Tracing-Id</th>
            <th>Order Amount</th>
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
                <td>{{ $order->sub_total}}</td>
            </tr>
            @php
                $count++;
            @endphp
        @endforeach

    </table>

</body>

</html>