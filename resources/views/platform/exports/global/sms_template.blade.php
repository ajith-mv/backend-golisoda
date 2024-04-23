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
            <th> Company Name </th>
            <th> Template Name </th>
            <th> PEID NO</th>
            <th> TDLT NO </th>
            <th> Header </th>
            <th> Template Name</th>
            <th> Sms Type</th>
            <th> Communication Type</th>
            <th> Template Content</th>
            <th> Added By </th>
            <th> Status </th>
          
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->company_name }}</td>
                <td>{{ $item->template_name }}</td>
                <td>{{ $item->peid_no }}</td>
                <td>{{ $item->tdlt_no }}</td>
                <td>{{ $item->header }}</td>
                <td>{{ $item->template_name }}</td>
                <td>{{ $item->sms_type }}</td>
                <td>{{ $item->communication_type }}</td>
                <td>{{ $item->template_content }}</td>
                <td>{{ $item->users_name }}</td>
                <td>{{  $item->status }}</td>
                
            </tr>
            @endforeach
        @endif
    </tbody>
</table>