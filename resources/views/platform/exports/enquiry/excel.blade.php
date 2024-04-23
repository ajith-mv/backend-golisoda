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
            <th>Enquiry Date</th>
            <th>First Name</th>
            <th>Email</th>
            <th>Mobile Number</th>
            <th>Message</th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->first_name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->mobile_no }}</td>
                <td>{{  $item->message }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>