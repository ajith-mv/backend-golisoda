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
            <th>Order No</th>
            <th>Order Date</th>
            <th>Payment ID</th>
            <th>Order Amount</th>
            <th>Order Status</th>
            <th>Payment Status </th>
            {{-- <th>RazorPay response </th> --}}
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->order_no }}</td>
                <td>{{ $item->order_date }}</td>
                <td>{{ $item->payment_no }}</td>
                <td>{{ $item->order_amount }}</td>
                <td>{{ $item->order_status_dd }}</td>
                <td>{{ $item->payment_status }}</td>
                {{-- <td>
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
                </td>   --}}
            </tr>
            @endforeach
        @endif
    </tbody>
</table>