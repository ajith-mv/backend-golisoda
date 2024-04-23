@extends('platform.layouts.template')
@section('toolbar')
<style>
    .content {
  padding: 10px 0;
}
</style>
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        @include('platform.layouts.parts._breadcrum')
        @include('platform.reports.products._export_button')
    </div>
</div>
@endsection
@section('content')
    <div id="kt_content_container" class="container-xxl">
        <div class="card">
            <div class="card-header border-0 pt-6 w-100">
                @include('platform.reports.products._filter_form')
            </div>
            <hr>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-2 mb-0 dataTable no-footer" id="product-table">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th> Order Date </th>
                                <th> Order No</th>
                                <th> Billing Info</th>
                                <th> Order Amount </th>
                                <th> Tax Amount </th>
                                <th> Shipping Amount </th>
                                <th> Coupon Amount </th>
                                <th> Discount Amount </th>
                                <th> Product SubTotal </th>
                                <th> Order Status</th>
                                <th> Payment Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
@endsection
@section('add_on_script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="{{ asset('assets/js/datatable.min.js') }}"></script>

    <script>
        $( document ).ready(function() {
            $('.date_range').val('');
});
        var dtTable = $('#product-table').DataTable({
           
            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                "url": "{{ route('reports.sale') }}",
                "data": function(d) {
                    return $('form#search-form').serialize() + "&" + $.param(d);
                }
            },
            columns: [
                {
                    data: 'created_at',
                    name: 'created_at',
                  
                },
                {
                    data: 'order_no',
                    name: 'order_no'
                },
                {
                    data: 'billing_info',
                    name: 'billing_info',
                    orderable: false,
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
               
                {
                    data: 'tax_amount',
                    name: 'tax_amount'
                },
                {
                    data: 'shipping_amount',
                    name: 'shipping_amount'
                },
                {
                    data: 'coupon_amount',
                    name: 'coupon_amount'
                },
                {
                    data: 'discount_amount',
                    name: 'discount_amount'
                },
                {
                    data: 'sub_total',
                    name: 'sub_total'
                },
                {
                    data: 'order_status',
                    name: 'order_status'
                },
                {
                    data: 'payment_status',
                    name: 'payment_status'
                }
               
            ],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-right"></i>', // or '→'
                    previous: '<i class="fa fa-angle-left"></i>' // or '←' 
                }
            },
            "aaSorting": [],
            "pageLength": 50
        });
       
        $('.dataTables_wrapper').addClass('position-relative');
        $('.dataTables_info').addClass('position-absolute');
        $('.dataTables_filter label input').addClass('form-control form-control-solid w-250px ps-14');
        $('.dataTables_filter').addClass('position-absolute end-0 top-0');
        $('.dataTables_length label select').addClass('form-control form-control-solid');

        $('#search-form').on('submit', function(e) {
            dtTable.draw();
            e.preventDefault();
        });
        $('#search-form').on('reset', function(e) {
            $('#filter_search_data').val('').trigger('change');
            $('#date_range').val('').trigger('change');
            $('#filter_product_name').val('');
            dtTable.draw();
            e.preventDefault();
        });

        $('.product-select2').select2();

        function exportProductExcel() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                xhrFields: {
                    responseType: 'blob',
                },
                url: "{{ route('reports.export.excel') }}",
                type: 'POST',
                data: $('form#search-form').serialize(),
                success: function(result, status, xhr) {

                    var disposition = xhr.getResponseHeader('content-disposition');
                    var matches = /"([^"]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] : 'salesreport.xlsx');

                    // The actual download
                    var blob = new Blob([result], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;

                    document.body.appendChild(link);

                    link.click();
                    document.body.removeChild(link);
                    
                }
            });

        }


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
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
            }
        }, cb);

        cb(start, end);
        
        // $('input[type="search"]').keyup(function(){           
        //     var search=$('input[type="search"]').val();
        //     if(search=='')
        //     {
        //         $('.date_range').val('').trigger('change');
        //         dtTable.draw();
        //     }
        // });
    </script>
@endsection
