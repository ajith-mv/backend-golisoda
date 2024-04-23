<div id="kt_content_container" class="">
    <div class="card">
        <div class="">
            <div class="table-responsive">
                <input type="hidden" id="customer_id" value="{{ $info->id }}">
                <table class="table align-middle table-row-dashed fs-6 gy-2 mb-0 dataTable no-footer" id="wishlist">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th> Product Name  </th>
                            <th> Price  </th>
                            <th> Created Date </th>
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
       
        var dtTable = $('#wishlist').DataTable({

            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                "url": "{{ route('customer-wishlist') }}",
                "data": function(d) {
                    d.customer_id = $('#customer_id').val();
                }
            },

            columns: [
               
                {
                    data: 'product_name',
                    name: 'product_name',
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
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
    </script>


