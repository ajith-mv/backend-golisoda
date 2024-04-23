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
            <th>Added Date</th>
            <th>Discount Name</th>
            <th>Discount Code</th>
            <th>Discount Sku</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Quantity </th>
            <th>Used Quantity </th>
            <th>Calculate Type </th>
            <th>Calculate Value </th>
            <th>Minimum Order Value</th>
            <th>Is Discount On </th>
            <th>Discount Type </th>
            <th>Repeated Use Count </th>
            <th>Order</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->coupon_name }}</td>
                <td>{{ $item->coupon_code }}</td>
                <td>{{ $item->coupon_sku }}</td>
                <td>{{ $item->start_date }}</td>
                <td>{{ $item->end_date }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->used_quantity }}</td>
                <td>{{ $item->calculate_type }}</td>
                <td>{{ $item->calculate_value }}</td>
                <td>{{ $item->minimum_order_value }}</td>
                <td>{{ $item->is_discount_on }}</td>
                <td>{{ $item->coupon_type }}</td>
                <td>{{ $item->repeated_use_count }}</td>
                <td>{{ $item->order_by }}</td>
                <td>{{  $item->status }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>