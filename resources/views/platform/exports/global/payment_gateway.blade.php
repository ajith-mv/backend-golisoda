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
            <th> Gateway </th>
            <th> Access Key </th>
            <th> Secret Key </th>
            <th> Merchant Id </th>
            <th> Working Key </th>
            <th> Is Primary </th>           
            <th> Added By </th>
            <th> Mode </th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->gateway->name  }}</td>
                <td>{{ $item->access_key }}</td>
                <td>{{ $item->secret_key }}</td>
                <td>{{ $item->merchant_id }}</td>
                <td>{{ $item->working_key }}</td>
                <td>{{ $item->is_primary }}</td>
                <td>{{ $item->userInfo->name }}</td>
                <td>{{ $item->mode }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>