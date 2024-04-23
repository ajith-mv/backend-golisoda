@if (isset($from) && $from == 'pdf')
    <style>
        table {
            border-spacing: 0;
            width: 100%;
        }

        table th,
        td {
            border: 1px solid;
        }
    </style>
@endif
<table>
    <thead>
        <tr>
            <th>Added Date</th>
            <th>Country</th>
            <th>State</th>
            <th>state Code</th>
            <th>Status</th>
            <th> Added By </th>
        </tr>
    </thead>
    <tbody>
        @if (isset($list) && !empty($list))
            @foreach ($list as $item)
                <tr>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->country_name }}</td>
                    <td>{{ $item->state_name }}</td>
                    <td>{{ $item->state_code }}</td>
                    <td>{{ $item->user_status }}</td>
                    <td>{{ $item->users_name }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
