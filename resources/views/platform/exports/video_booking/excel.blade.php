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
            <th>Customer Name</th>
            <th>Product Name</th>
            <th>Contact Name</th>
            <th>Contact Email</th>
            <th>Contact Phone</th>
            <th>Reach Type</th>
            <th>Preferred Date</th>
            <th>Preferred Time</th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->first_name }}</td>
                <td>{{ $item->product_name ?? null}}</td>
                <td>{{ $item->contact_name }}</td>
                <td>{{ $item->contact_email }}</td>
                <td>{{ $item->contact_phone }}</td>
                <td>{{ $item->reach_type }}</td>
                <td>{{ $item->preferred_date }}</td>
                <td>{{  $item->preferred_time }}</td>
                
            </tr>
            @endforeach
        @endif
    </tbody>
</table>