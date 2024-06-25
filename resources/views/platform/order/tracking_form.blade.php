{{-- @section('content') --}}
@foreach ($brandOrders as $brandOrder)
    <div class="card mb-3">
        <div class="card-header">
            {{ $brandOrder->brand_name }}
        </div>
        <div class="card-body row">
            <div class="form-group col-md-6">
                <label for="tracking_id_{{ $brandOrder->brand_id }}">Tracking ID</label>
                <input type="text" class="form-control" id="tracking_id_{{ $brandOrder->brand_id }}"
                    name="tracking_id[{{ $brandOrder->brand_id }}]"
                    value="{{ $brandOrder->tracking_id ?? '' }}">
            </div>

            <div class="form-group col-md-6">
                <label for="estimated_arrival_date_{{ $brandOrder->brand_id }}">Estimated Arrival Date</label>
                <input type="text" class="form-control datepicker" readonly placeholder="Select date"
                    id="estimated_arrival_date_{{ $brandOrder->brand_id }}"
                    name="estimated_arrival_date[{{ $brandOrder->brand_id }}]"
                    value="{{ $brandOrder->estimated_arrival_date ?? '' }}">

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
