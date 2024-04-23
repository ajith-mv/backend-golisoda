@extends('platform.layouts.template')
@section('toolbar')
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        @include('platform.layouts.parts._breadcrum')
        @include('platform.reports.payment_transaction._export_button')
    </div>
</div>
@endsection
@section('content')

    <style>
        .paginate_button {
            padding: 5px 14px;
        }
        a.paginate_button.current {
            background: #009EF7;
            color: white;
            border-radius: 5px;
        }
    </style>

    <div id="kt_content_container" class="container-xxl">
        <div class="card">
            <div class="card-header border-0 pt-6 w-100">
                <div class="card-toolbar w-100">
                    <div class="d-flex justify-content-end w-100" data-kt-customer-table-toolbar="base">
                        @include('platform.reports.payment_transaction._filter_form')                    
                    </div>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-2 mb-0 dataTable no-footer" id="payment-table">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                              
                                <th> Order No  </th>
                                <th> Order Date  </th>
                                <th> Payment ID  </th>
                                <th> Order Amount </th>
                                <th> Order Status </th>
                                <th> Payment Status </th>
                                <th style="width: 130px;"> Action </th>
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
        var dtTable = $('#payment-table').DataTable({

            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                "url": "{{ route('reports.payment') }}",
                "data": function(d) {                 
                    return $('form#search-form').serialize() + "&" + $.param(d);
                }
            },
            columns: [
                {
                    data: 'order_no',
                    name: 'order_no',
                    orderable: false,
                },
                {
                    data: 'order_date',
                    name: 'order_date',
                    orderable: false,
                },
                {
                    data: 'payment_no',
                    name: 'payment_no',
                    orderable: false,
                },
                {
                    data: 'order_amount',
                    name: 'order_amount',
                    orderable: false,
                },
                {
                    data: 'order_status_dd',
                    name: 'order_status_dd',
                    orderable: false,
                },           
                {
                    data: 'payment_status',
                    name: 'payment_status',
                    orderable: false,
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-right"></i>', // or '→'
                    previous: '<i class="fa fa-angle-left"></i>' // or '←' 
                }
            },
            "aaSorting": [],
            "pageLength": 25
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
            $('.date_range').val('').trigger('change');
            $('#filter_product_name').val('');           
            dtTable.draw();
            e.preventDefault();
        });

        function viewPayments(id) {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('payment.view') }}",
                type: 'POST',
                data: {id:id},
                success: function(res) {
                    $( '#form-common-content' ).html(res);
                    const drawerEl = document.querySelector("#kt_common_add_form");
                    const commonDrawer = KTDrawer.getInstance(drawerEl);
                    commonDrawer.show();
                    return false;
                }, error: function(xhr,err){
                    if( xhr.status == 403 ) {
                        toastr.error(xhr.statusText, 'UnAuthorized Access');
                    }
                }
            });

        }
        

        function exportPaymentExcel() {

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    xhrFields: {
        responseType: 'blob',
    },
    url: "{{ route('payment-reports.export.excel') }}",
    type: 'POST',
    data: $('form#search-form').serialize(),
    success: function(result, status, xhr) {

        var disposition = xhr.getResponseHeader('content-disposition');
        var matches = /"([^"]*)"/.exec(disposition);
        var filename = (matches != null && matches[1] ? matches[1] : 'paymentreport.xlsx');

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


    </script>
@endsection
