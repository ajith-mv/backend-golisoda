@if( isset( $from ) && $from == 'pdf')
<style>
    table{ border-spacing: 0;width:100%; }
    table th,td {
        border:1px solid;
    }
</style>
@endif
<table>
    <thead>
        <tr>
            <th> Order Date </th>
            <th> Order No </th>
            <th> Billing Name </th>
            <th> Billing Email</th>
            <th> Billing Mobile no</th>
            <th> Billing Address </th>
            <th> Billing City </th>
            <th> Billing State </th>
            <th> Product SubTotal </th>
            <th> Tax Amount</th>
            <th> Tax Percentage </th>
            <th> Coupon Amount </th>
            <th> Shipping Amount </th>
            <th> Order Amount </th>
            <th> Order Status </th>
            <th> Payment id </th>
            <th> Payment Mode </th>

        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->order_no }}</td>
               
                <td> {{ $item->billing_name }} </td>
                <td> {{ $item->billing_email }}</td>
                <td> {{ $item->billing_mobile_no }}</td>
                <td> {{ $item->billing_address_line1 }}</td>
                <td> {{ $item->billing_city }}</td>
                <td> {{ $item->billing_state }}</td>
                <td>{{ $item->sub_total }}</td>
                <td> {{ $item->tax_amount }} </td>
                <td> {{ $item->tax_percentage }} %</td>
                <td> {{ $item->coupon_amount }} </td>
                <td> {{ $item->shipping_amount }}</td>
                <td>{{ $item->amount }}</td>
                <td> {{ $item->status }}</td>
                <td> {{ $item->payment_response_id }}</td>
                <td> {{ isset($item->payments) ? $item->payments->payment_type : '' }}</td>

            </tr>
            @endforeach
        @endif
    </tbody>
</table>