<div id="kt_content_container" class="">
    <div class="card">
        <div class="">
            <div class="table-responsive">
                <input type="hidden" id="customer_id" value="{{ $info->id }}">
                <table class="table align-middle table-row-dashed fs-6 gy-2 mb-0 dataTable no-footer" id="customer_order">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th> Order Date </th>
                            <th> Order No  </th>
                            <th> Amount </th>
                            <th> Tax Amount </th>
                            <th> Shipping Info </th>
                            <th> Billing Info </th>
                            <th> Order Status </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end::Card-->
</div>
    <script src="{{ asset('assets/js/datatable.min.js') }}"></script>
    <script>
       
        var dtTable = $('#customer_order').DataTable({

            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                "url": "{{ route('customer.order') }}",
                "data": function(d) {
                    d.customer_id = $('#customer_id').val();
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
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'tax_amount',
                    name: 'tax_amount'
                },
                {
                    data: 'shipping_info',
                    name: 'shipping_info'
                },
                {
                    data: 'billing_info',
                    name: 'billing_info'
                },
                {
                    data: 'status',
                    name: 'status'
                }
                
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
    </script>


