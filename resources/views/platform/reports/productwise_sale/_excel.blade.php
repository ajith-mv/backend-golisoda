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
            <th> Category Name </th>
            <th> Product Name </th>
            <th> No. of Qty Sold </th>
            <th> Amount </th>
            
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->category_name }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->order_quantity }}</td>
                <td>{{ $item->prod_amount }}</td>                
            </tr>
            @endforeach
        @endif
    </tbody>
</table>