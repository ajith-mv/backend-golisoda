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
            <th> Customer Name </th>
            <th> No. of Orders </th>
            <th> No. of Products </th>
            <th> Coupon Amt </th>
            <th> Discount Amt </th>
            <th> Order Amt </th>
            
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->first_name }}</td>
                <td>{{ $item->total_order }}</td>
                <td>{{ $item->total_products }}</td>
                <td>{{ $item->cus_coupon_amount }}</td>
                <td>{{ $item->discount_amount }}</td>
                <td>{{ $item->order_amount }}</td>                
            </tr>
            @endforeach
        @endif
    </tbody>
</table>