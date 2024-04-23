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
            <th>Brand Name</th>
            <th>Email</th>
            <th>Contact Number</th>
            <th>Description</th>
            <th>Address</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Order By</th>
            <th>Added By</th>
            <th>Status</th>
          
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->brand_name ?? '' }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->contact_no }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->address }}</td>
                <td>{{ $item->latitude }}</td>
                <td>{{ $item->longitude }}</td>
                <td>{{ $item->order_by }}</td>
                <td>{{ $item->users_name }}</td>
                <td>{{  $item->status }}</td>
                
            </tr>
            @endforeach
        @endif
    </tbody>
</table>