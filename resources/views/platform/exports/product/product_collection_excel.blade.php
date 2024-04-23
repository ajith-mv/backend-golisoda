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
            <th> Collection Name </th>
            <th> Tag Line </th>
            <th> Show in Home Page </th>
            <th> Products </th>
            <th> Status </th>
            <th> Added At </th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->collection_name }}</td>
                <td>{{ $item->tag_line ?? '' }}</td>
                <td>{{ $item->show_home_page }}</td>
                <td>{{ count($item->collectionProducts) }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->created_at }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>