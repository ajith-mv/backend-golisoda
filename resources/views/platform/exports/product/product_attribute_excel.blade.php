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
            <th> Title </th>
            <th> Tag Line </th>
            <th> Can Searchable </th>
            <th> Can Comparable </th>
            <th> Can use in Product List </th>
            <th> Status </th>
            <th> Added At </th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->title }}</td>
                <td>{{ $item->tag_line ?? '' }}</td>
                <td>{{ ($item->is_searchable) ? 'Yes' : 'No' }}</td>
                <td>{{ ($item->is_comparable) ? 'Yes' : 'No' }}</td>
                <td>{{ ($item->is_use_in_product_listing) ? 'Yes' : 'No' }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->created_at }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>