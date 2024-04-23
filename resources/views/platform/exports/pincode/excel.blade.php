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
            <th>Pincode</th>
            <th>Description</th>
            <th>Added by</th>
            <th>Status</th>
          
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->pincode }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->users_name }}</td>
                <td>{{  $item->user_status }}</td>
                
            </tr>
            @endforeach
        @endif
    </tbody>
</table>