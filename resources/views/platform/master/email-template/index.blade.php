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
                    <div class="d-flex justify-content-end w-100" data-kt-country-table-toolbar="base">
                    @if( access()->hasAccess('email-template', 'filter') )    
                        @include('platform.master.email-template._filter')
                    @endif

                        <button type="button" class="btn btn-primary" onclick="return openForm('email-template')">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                        rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                        fill="currentColor" />
                                </svg>
                            </span>
                            Add Email Template
                        </button>

                    </div>

                </div>

            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-2 mb-0 dataTable no-footer" id="country-table">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th> Title </th>
                                <th> Email Type</th>
                                <th> Created Date </th>
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
        var dtTable = $('#country-table').DataTable({

            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                "url": "{{ route('email-template') }}",
                "data": function(d) {
                    d.status = $('select[name=filter_status]').val();
                    d.filter_subCategory = $('select[name=filter_subCategory]').val();
                }
            },

            columns: [{
                    data: 'title',
                    name: 'title',
                  
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
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
            "aaSorting": [],
            "pageLength": 25
        });
        $('.dataTables_wrapper').addClass('position-relative');
        $('.dataTables_info').addClass('position-absolute');
        $('.dataTables_filter label input').addClass('form-control form-control-solid w-250px ps-14');
        $('.dataTables_filter').addClass('position-absolute end-0 top-0');
        $('.dataTables_length label select').addClass('form-control form-control-solid');

        $('#search-form').on('submit', function(e) {
            var filter_val =  $('#filter_subCategory').val();
            if(filter_val == "published" || "unpublished")
            {
                $('#btn-light-primary').removeClass('btn-light-primary');
                $('#btn-light-primary').addClass('btn-light-danger');
            }
            if(filter_val == "0" || null)
            {
                $('#btn-light-primary').addClass('btn-light-primary');
                $('#btn-light-primary').removeClass('btn-light-danger');
            }
            var filter_val =  $('#filter_status').val();
            if(filter_val == "published" || "unpublished")
            {
                $('#btn-light-primary').removeClass('btn-light-primary');
                $('#btn-light-primary').addClass('btn-light-danger');
            }
            if(filter_val == "0" || null)
            {
                $('#btn-light-primary').addClass('btn-light-primary');
                $('#btn-light-primary').removeClass('btn-light-danger');
            }
            dtTable.draw();
            e.preventDefault();
        });
        $('#search-form').on('reset', function(e) {
            $('select[name=filter_status]').val(0).change();
            $('select[name=filter_subCategory]').val(0).change();

            dtTable.draw();
            e.preventDefault();
        });
    </script>
@endsection
