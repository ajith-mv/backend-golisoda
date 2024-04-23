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

        <div class="card mb-2 mb-xl-5">
            <div class="card-body pt-1 pb-0">

                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">

                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5 global-class active" id="general"
                            href="#" onclick="return getGlobalTab('general')">Project</a>
                    </li>

                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5 global-class" id="sms" href="#"
                            onclick="return getGlobalTab('sms')">SMS</a>
                    </li>

                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5 global-class" id="payment" href="#"
                            onclick="return getGlobalTab('payment')">Payment</a>
                    </li>

                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5 global-class" id="links" href="#"
                            onclick="return getGlobalTab('links')"> Links </a>
                    </li>

                </ul>

            </div>
        </div>


        <div class="card mb-5 mb-xl-10">
            <div id="kt_account_settings_profile_details" class="collapse show">
                @include('platform.global._general_form')
            </div>
        </div>

    </div>
@endsection
@section('add_on_script')
    <script>
        const global_form_submit_url = "{{ route('global.save') }}";

        function getGlobalTab(tabType) {
            $('a.global-class').removeClass('active');
            $('#' + tabType).addClass('active');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('global.tab') }}",
                type: 'POST',
                data: {
                    tabType: tabType
                },
                success: function(res) {
                    $('#kt_account_settings_profile_details').html(res);
                }
            });
        }

        $("body").on("click", "#rowAdder", function() {
            
            newRowAdd =
                '<div id="row" class="row p-7">'+
                '<div class="col-sm-3">'+
                    '<input type="text" name="link_name[]" class="form-control" placeholder="Link Name" required>'+
                '</div>'+
                '<div class="col-sm-2">'+
                    '<input type="text" name="link_icon[]" class="form-control" placeholder="Link Icon">'+
                '</div>'+
                '<div class="col-sm-5">'+
                    '<input type="text" name="link_url[]" class="form-control" placeholder="Link Url" required>'+
                '</div>'+
                '<div class="col-sm-2">'+
                    '<div class="input-group mt-1">'+
                        '<div class="input-group-prepend">'+
                            '<button class="btn btn-danger btn-sm" id="DeleteRow" type="button">'+
                                '<i class="bi bi-trash"></i>'+
                                'Delete'+
                            '</button>'+
                        '</div>'+                        
                    '</div>'+
                '</div>'+
            '</div>';

            $('#newinput').append(newRowAdd);
        })

        $("body").on("click", "#DeleteRow", function() {
            $(this).parents("#row").remove();
        })
    </script>

    <script src="{{ asset('assets/js/custom/account/settings/global-details.js') }}"></script>
@endsection
