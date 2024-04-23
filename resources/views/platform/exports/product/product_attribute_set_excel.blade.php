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
            <th> SKU </th>
            <th> Header </th>
            <th> Is_Searchable </th>
            <th> Is_Overview </th>
            <th> Keys </th>
            <th> Values </th>
            <th> Sorting Order </th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->prod_sku }}</td>
                <td>{{ $item->att_title }}</td>
                <td>{{ ($item->search) ? 'Yes' : 'No' }}</td>
                <td>{{ ($item->overview) ? 'Yes' : 'No' }}</td>
                <td>{{  $item->keys }}</td>
                <td>{{ $item->value }}</td>
                <td>{{ $item->order_by_att }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>