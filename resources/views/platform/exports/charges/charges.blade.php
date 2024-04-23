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
            <th>Title</th>
            <th>Minimum Order Amount</th>
            <th>Charges</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->shipping_title }}</td>
                <td>{{ $item->minimum_order_amount }}</td>
                <td>{{ $item->charges }}</td>
                <td>{{  $item->status }}</td>
                
            </tr>
            @endforeach
        @endif
    </tbody>
</table>