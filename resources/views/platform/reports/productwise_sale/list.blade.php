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
        @include('platform.reports.productwise_sale._export_button')
    </div>
</div>
@endsection
@section('content')
    <div id="kt_content_container" class="container-xxl">
        <div class="card">
            <div class="card-header border-0 pt-6 w-100">
                @include('platform.reports.productwise_sale._filter_form')
            </div>
            <hr>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-2 mb-0 dataTable no-footer" id="product-table">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                             
                                <th> Category Name</th>
                                <th> Product Name</th>
                                <th> No. of Qty Sold</th>
                                <th> Amount </th>
                                <th> Order Status </th>
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
                "url": "{{ route('reports.productwise') }}",
                "data": function(d) {
                    console.log(d);
                    return $('form#search-form').serialize() + "&" + $.param(d);
                }
            },
            columns: [
                
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'order_quantity',
                    name: 'order_quantity'
                },
                {
                    data: 'prod_amount',
                    name: 'prod_amount'
                },
                {
                    data: 'status',
                    name: 'status'
                },
               
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
            $('.date_range').val('').trigger('change');
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


//         $('input[type="search"]').keyup(function(){           
//             var search=$('input[type="search"]').val();
//             if(search=='')
//             {
//                 $('.date_range').val('').trigger('change');
//                 dtTable.draw();
//             }
// });


        
    </script>
@endsection
