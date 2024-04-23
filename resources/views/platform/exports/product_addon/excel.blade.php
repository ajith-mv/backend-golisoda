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
            <th>Product</th>
            <th>Description</th>
            <th>Status</th>
            <th>Added by</th>
            <th>Order by</th>
          
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{  $item->status }}</td>
                <td>{{ $item->users_name }}</td>
                <td>{{ $item->order_by }}</td>


                
            </tr>
            @endforeach
        @endif
    </tbody>
</table>