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
            <th>Product Category Name</th>
            <th>Parent Category</th>
            <th>Status</th>
            <th>Tag Line</th>
            <th>Tax</th>
            <th> Added By </th>
            <th> Added At </th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
           
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->parent->name ?? '' }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->tag_line ?? '' }}</td>
                <td>{{ $item->tax->title ?? '' }}</td>
                <td>{{ $item->userInfo->name ?? '' }}</td>
                <td>{{ $item->created_at }}</td>

            </tr>
            @endforeach
        @endif
    </tbody>
</table>