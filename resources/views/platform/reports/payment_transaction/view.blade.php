<!--begin::Header-->
<div class="card-header" id="kt_activities_header">
    <h3 class="card-title fw-bolder text-dark">{{ $modal_title ?? 'Form Action' }}</h3>
    <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
            <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                        transform="rotate(-45 6 17.3137)" fill="currentColor" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                        transform="rotate(45 7.41422 6)" fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </button>
    </div>
</div>

<div class="card-body">

    {{-- {{ dd( gettype(unserialize($payment_info->response)['notes'] ) ) }} --}}
    <div>
        <table class="table table-striped table-hover">
            <tr>
                <th>Payment No</th>
                <td>{{ $payment_info->payment_no }}</td>
            </tr>
            <tr>
                <th>Order Amount</th>
                <td>{{ $payment_info->amount }}</td>
            </tr>
            <tr>
                <th>Paid Amount</th>
                <td>{{ $payment_info->paid_amount }}</td>
            </tr>
            <tr>
                <th>Payment Type </th>
                <td>{{ $payment_info->payment_type }}</td>
            </tr>
            <tr>
                <th>Payment Mode </th>
                <td>{{ $payment_info->payment_mode }}</td>
            </tr>
            <tr>
                <th>Payment Status </th>
                <td>{{ $payment_info->status }}</td>
            </tr>
            <tr>
                <th>Payment Date </th>
                <td>{{ date( 'd M Y H:i A', strtotime($payment_info->created_at)) }}</td>
            </tr>
            @if( isset( $payment_info->response ) && !empty( $payment_info->response ) ) 
            @foreach ( unserialize($payment_info->response) as $itemkey => $itemvalue)
                <tr>
                    <th>{{ $itemkey }}</th>
                    <td>
                        @if(gettype($itemvalue) == 'object')
                            @if( isset( $itemvalue ) && !empty( $itemvalue ) )
                                @foreach ($itemvalue as $item)
                                    <div>{{ $item }}</div>
                                @endforeach
                            @endif
                        @else 
                        {{ $itemvalue }}
                        @endif
                    </td>
                </tr>
            @endforeach
            @endif
        </table>
    </div>
</div>
<div class="card-footer">
    <div>
        <button type="button" class="btn btn-sm btn-active-light-primary" id="kt_activities_close">Close</button>
    </div>
</div>
