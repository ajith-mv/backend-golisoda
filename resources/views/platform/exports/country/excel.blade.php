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
            <th>Name</th>
            <th>Nick Name</th>
            <th>ISO</th>
            <th>ISO 3</th>
            <th>Number Code</th>
            <th>Phone Code</th>
            <th>Status</th>
          
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->nice_name }}</td>
                <td>{{ $item->iso }}</td>
                <td>{{ $item->iso3 }}</td>
                <td>{{ $item->num_code }}</td>
                <td>{{ $item->phone_code }}</td>
                <td>{{  $item->user_status }}</td>
                
            </tr>
            @endforeach
        @endif
    </tbody>
</table>