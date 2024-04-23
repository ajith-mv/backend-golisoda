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
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Mobile Number</th>
            <th>Customer No</th>
            <th>Password </th>
            <th>DOB </th>
            <th>Address</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->first_name }}</td>
                <td>{{ $item->last_name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->mobile_no }}</td>
                <td>{{ $item->customer_no }}</td>
                <td>{{ $item->password }}</td>
                <td>{{ $item->dob }}</td>
                <td>{{ $item->address }}</td>
                <td>{{  $item->status }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>