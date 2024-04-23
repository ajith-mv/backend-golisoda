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
            <th> Subscription Date </th>
            <th> Email </th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list) )
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->email }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>