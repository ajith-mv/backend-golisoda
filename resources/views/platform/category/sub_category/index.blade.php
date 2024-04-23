@extends('platform.layouts.template')
@section('toolbar')
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        @include('platform.layouts.parts._breadcrum')
    </div>
</div>
@endsection
@section('content')
    <div id="kt_content_container" class="container-xxl">
        <div class="card">
            <div class="card-header border-0 pt-6 w-100">
                <div class="card-toolbar w-100">
                    <div class="d-flex justify-content-end w-100" data-kt-sub_category-table-toolbar="base">
                        @if( access()->hasAccess($routeName, 'filter') )
                        <button type="button" class="btn btn-light-primary me-3"  id="btn-light-primary" data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-end">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <path
                                        d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                            Filter
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5" data-kt-sub_category-table-filter="form">
                                <form id="search-form">
                                    <!--begin::Input group-->
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-bold">Status:</label>
                                        <select name="filter_status" id="filter_status" class="form-select form-select-solid fw-bolder"
                                            data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true"
                                            data-kt-sub_category-table-filter="order" data-hide-search="true">
                                            <option value="0">All</option>
                                            <option value="published">Published</option>
                                            <option value="unpublished">Unpublished</option>
                                        </select>
                                    </div>
                                    @if( $showFilterCategory )
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-bold">Category:</label>
                                        <select name="filter_category" id="filter_category" class="form-select form-select-solid fw-bolder"
                                            data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true"
                                            data-kt-sub_category-table-filter="order" data-hide-search="true">
                                            <option value="0">All</option>
                                           @foreach($category as $key=>$val)
                                            <option value="{{ $val->category_name }}">{{ $val->category_name }}</option>
                                           @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-end">
                                        <button type="reset"
                                            class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                            data-kt-menu-dismiss="true" data-kt-sub_category-table-filter="reset">Reset</button>
                                        <button type="submit" class="btn btn-primary fw-bold px-6"
                                            data-kt-menu-dismiss="true" data-kt-sub_category-table-filter="filter">Apply</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        @include('platform.layouts.parts.common._export_button')

                        <button type="button" class="btn btn-primary" onclick="return openForm('{{ $routeName }}')">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                        rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                        fill="currentColor" />
                                </svg>
                            </span>
                            Add {{ $title }}
                        </button>

                    </div>

                </div>

            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-2 mb-0 dataTable no-footer" id="sub_category-table">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th> Image </th>
                                @if( $routeName == 'product-tags')
                                <th> Product Tag </th>
                                @elseif( $routeName == 'product-labels')
                                <th> Product Label  </th>
                                @else
                                <th> Category Name  </th>
                                <th> SubCategory Name  </th>
                                @endif
                                <th> Slug </th>
                                <th> Created By </th>
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
        @if( $routeName == 'sub_category')
        var columnFiled = [
                {
                    data: 'image',
                    name: 'image',
                  
                },
                {
                    data: 'category_name',
                    name: 'category_name',
                  
                },
                {
                    data: 'name',
                    name: 'name',
                  
                },
                {
                    data: 'slug',
                    name: 'slug',
                  
                },
                {
                    data: 'users_name',
                    name: 'users_name'
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
            ];
      
        @else
        var columnFiled = [
                {
                    data: 'image',
                    name: 'image',
                  
                },
                {
                    data: 'name',
                    name: 'name',
                  
                },
                {
                    data: 'slug',
                    name: 'slug',
                  
                },
                {
                    data: 'users_name',
                    name: 'users_name'
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
            ];
        @endif

        var dtTable = $('#sub_category-table').DataTable({
      
            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                "url": "{{ route('sub_category') }}",
                "data": function(d) {
                    d.status = $('select[name=filter_status]').val();
                    d.filter_category = $('select[name=filter_category]').val();
                    d.page_type = '{{ $routeName }}';
                }
            },

            columns: columnFiled,
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
            var filter_val =  $('#filter_category').val();
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
            $('select[name=filter_category]').val(0).change();

            dtTable.draw();
            e.preventDefault();
        });
    </script>
@endsection
