@extends('platform.layouts.template')
@section('toolbar')
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        @include('platform.layouts.parts._breadcrum')
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
                        @if( access()->hasAccess('order', 'export') )
                            @include('platform.order._filter')
                        @endif
                        @include('platform.layouts.parts.common._export_button')
                    </div>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-2 mb-0 dataTable no-footer" id="order-table">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th> Order Date  </th>
                                <th> Order No  </th>
                                <th> Billing Info </th>
                                <th> Order Amount </th>
                                <th> Order Quantity </th>
                                 <th> Payment Type </th>
                                <th> Payment Status </th>
                                <th> Order Status </th>
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
        var dtTable = $('#order-table').DataTable({

            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                "url": "{{ route('order') }}",
                "data": function(d) {
                    d.status = $('select[name=filter_status]').val();
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
                    name: 'billing_info'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'order_quantity',
                    name: 'order_quantity'
                },
                 {
                    data: 'payment_type',
                    name: 'payment_type'
                },
               
                {
                    data: 'payment_status',
                    name: 'payment_status'
                },
                {
                    data: 'order_status',
                    name: 'order_status'
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
            $('select[name=filter_status]').val(0).change();

            dtTable.draw();
            e.preventDefault();
        });


        function viewOrder(id) {
       
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('order.view') }}",
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

        function openOrderStatusModal(id) {
       
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('order.status.modal') }}",
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
    </script>
@endsection
