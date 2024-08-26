{{-- @section('content') --}}
@foreach ($brandOrders as $brandOrder)
    <div class="card mb-3">
        <div class="card-header">
            {{ $brandOrder->brand_name }}
        </div>
        <div class="card-body row">
            <div class="form-group col-md-5">
                <label for="tracking_id_{{ $brandOrder->brand_id }}">Tracking ID</label>
                <input type="text" class="form-control" id="tracking_id_{{ $brandOrder->brand_id }}"
                    name="tracking_id[{{ $brandOrder->brand_id }}]"
                    value="{{ $brandOrder->tracking_id ?? '' }}">
            </div>

            <div class="form-group col-md-4">
                <label for="estimated_arrival_date_{{ $brandOrder->brand_id }}">Estimated Arrival Date</label>
                <input type="text" class="form-control datepicker" readonly placeholder="Select date"
                    id="estimated_arrival_date_{{ $brandOrder->brand_id }}"
                    name="estimated_arrival_date[{{ $brandOrder->brand_id }}]"
                    value="{{ $brandOrder->estimated_arrival_date ?? '' }}">

            </div>
            <div class="col-md-3">
                <label>Download Invoice</label>
                <div class="">
                    <a target="_blank" href="{{asset('storage/invoice_order/' . $brandOrder->brand_id . '/' . $info->order_no . '.pdf')}}" tooltip="Download Invoice"  class="btn btn-icon btn-active-success btn-light-success mx-1 w-42px h-42px" > 
                        <i class="fa fa-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach


<script>
    $('.datepicker').flatpickr({
        enableTime: false,
        dateFormat: "Y-m-d",
    });
</script>
