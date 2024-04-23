@extends('platform.layouts.template')
@section('toolbar')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex text-dark fw-bolder fs-3 align-items-center my-1"> Golisoda Dashboard </h1>
            </div>
            <div class="d-flex flex-wrap">
                <div class="form-group" style="width: 250px">
                    <input class="form-control form-control-solid w-100" name="date_range" placeholder="Pick date range"
                        id="kt_ecommerce_report_views_daterangepicker" required />
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="getDashboardData()">Submit</button>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ url('products/add') }}" class="btn btn-sm btn-light">Add Product </a>
                <a href="{{ url('users') }}" class="btn btn-sm btn-primary">All Users</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div id="kt_content_container" class="container-xxl">
        @include('platform.dashboard._dynamic_data')
    </div>
@endsection
@section('add_on_script')
    <!--begin::Page Vendors Javascript(used by this page)-->
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/vis-timeline/vis-timeline.bundle.js') }}"></script>
    <script>
        var start = moment().subtract(29, "days");
        var end = moment();
        var input = $("#kt_ecommerce_report_views_daterangepicker");

        function cb(start, end) {
            input.html(start.format("D MMMM, YYYY") + " - " + end.format("D MMMM, YYYY"));
        }
        
        input.daterangepicker({
            startDate: start,
            endDate: end,
            locale: {
                format: 'DD/MMM/YYYY'
            },
            ranges: {
                "Today": [moment(), moment()],
                "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf(
                    "month")]
            }
        }, cb);

        cb(start, end);

        function getDashboardData() {
            var date_range = $('#kt_ecommerce_report_views_daterangepicker').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('home.view') }}",
                type: "POST",
                data: {
                    date_range: date_range
                },
                success: function(res) {
                    $('#kt_content_container').html(res);
                }
            })
        }
    </script>
@endsection
