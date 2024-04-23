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
            <th>Customer Name</th>
            <th>Product Name</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->customer_name }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->star }}</td>
                <td>{{ $item->comments }}</td>
                <td>{{  $item->status }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>