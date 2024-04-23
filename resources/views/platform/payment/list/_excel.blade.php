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
            <th>Payment Date</th>
            <th>Payment No</th>
            <th>Order No</th>
            <th>Order Amount</th>
            <th>Paid Amount</th>
            <th>Payment Status </th>
            <th>RazorPay response </th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->payment_no }}</td>
                <td>{{ $item->orders->order_no }}</td>
                <td>{{ $item->amount }}</td>
                <td>{{ $item->paid_amount }}</td>
                <td>{{ $item->status }}</td>
                <td>
                    @if( isset( $item->response ) && !empty( $item->response ) )
                        @foreach ( unserialize( $item->response ) as $itemkey => $itemvalue )
                            <div>
                                {{ $itemkey }} : 
                                @if( gettype($itemvalue) == 'object')
                                    @foreach ($itemvalue as $item)
                                    <div> {{ $item }}, </div>
                                    @endforeach
                                @else
                                    {{ $itemvalue }}
                                @endif
                            </div>
                        @endforeach
                    @endif
                </td>  
            </tr>
            @endforeach
        @endif
    </tbody>
</table>