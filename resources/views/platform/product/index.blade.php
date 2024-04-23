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
        @include('platform.layouts.parts._menu_add_button')
    </div>
</div>
@endsection
@section('content')
    <div id="kt_content_container" class="container-xxl">
        <div class="card">
            <div class="card-header border-0 pt-6 w-100">
                <!--@if( access()->hasAccess('products', 'filter') )-->
                   
                <!--@endif-->
               
                 @include('platform.product._filter_form')
            </div>
            <hr>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-2 mb-0 dataTable no-footer" id="product-table">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Product Image</th>
                                <th> Product </th>
                                <th> SKU </th>
                                <th> Category </th>
                                <th> HSN Code </th>
                                <th> Brand  </th>
                                <th> Price </th>
                                <th> Qty </th>
                                <th> Stock Status </th>
                                <th> Status </th>
                                <th style="width: 75px;">Action</th>
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
        var dtTable = $('#product-table').DataTable({

            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                "url": "{{ route('products') }}",
                "data": function(d) {
                    return $('form#search-form').serialize() + "&" + $.param(d);
                }
            },
            columns: [
                {
                    data: 'product_image',
                    name: 'Product Image',
                  
                },
                {
                    data: 'product_name',
                    name: 'product_name',
                  
                },
                {
                    data: 'sku',
                    name: 'sku'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'hsn_code',
                    name: 'hsn_code'
                },
                {
                    data: 'brand_name',
                    name: 'brand_name'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                
                {
                    data: 'stock_status',
                    name: 'stock_status'
                },
                {
                    data: 'status',
                    name: 'status'
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
            "columnDefs": [
                { "orderable": false, "targets": 0 } // Disable sorting for the first column
               ],
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
            $('#filter_product_category').val('').trigger('change');
            $('#filter_brand').val('').trigger('change');
            $('#filter_label').val('').trigger('change');
            $('#filter_tags').val('').trigger('change');
            $('#filter_stock_status').val('').trigger('change');
            $('#filter_product_status').val('').trigger('change');
            $('#filter_product_name').val('');
            $('#filter_video_booking').val('').trigger('change');
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
                url: "{{ route('products.export.excel') }}",
                type: 'POST',
                data: $('form#search-form').serialize(),
                success: function(result, status, xhr) {

                    var disposition = xhr.getResponseHeader('content-disposition');
                    var matches = /"([^"]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] : 'product_masters.xlsx');

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
    </script>
@endsection
