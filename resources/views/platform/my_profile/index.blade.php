@extends('platform.layouts.template')
@section('toolbar')
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        @include('platform.layouts.parts._breadcrum')
    </div>
</div>
@endsection
@section('content')
<div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
        <div class="flex-lg-row-fluid ms-lg-15">
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-8" id="tabUL">
                <li class="nav-item current" >
                    <a class="nav-link text-active-primary pb-4 active myaccount get-tab " data-id="myaccount" onclick="return getTab('myaccount')"  href="javascript:void(0)" >
                    <span class="svg-icon svg-icon-2 me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M11 2.375L2 9.575V20.575C2 21.175 2.4 21.575 3 21.575H9C9.6 21.575 10 21.175 10 20.575V14.575C10 13.975 10.4 13.575 11 13.575H13C13.6 13.575 14 13.975 14 14.575V20.575C14 21.175 14.4 21.575 15 21.575H21C21.6 21.575 22 21.175 22 20.575V9.575L13 2.375C12.4 1.875 11.6 1.875 11 2.375Z" fill="currentColor" />
                        </svg>
                    </span>
                    My Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4 password get-tab "  data-id="password" onclick="return getTab('password')"  href="javascript:void(0)">
                    
                    <span class="svg-icon svg-icon-2 me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M18 10V20C18 20.6 18.4 21 19 21C19.6 21 20 20.6 20 20V10H18Z" fill="currentColor" />
                            <path opacity="0.3" d="M11 10V17H6V10H4V20C4 20.6 4.4 21 5 21H12C12.6 21 13 20.6 13 20V10H11Z" fill="currentColor" />
                            <path opacity="0.3" d="M10 10C10 11.1 9.1 12 8 12C6.9 12 6 11.1 6 10H10Z" fill="currentColor" />
                            <path opacity="0.3" d="M18 10C18 11.1 17.1 12 16 12C14.9 12 14 11.1 14 10H18Z" fill="currentColor" />
                            <path opacity="0.3" d="M14 4H10V10H14V4Z" fill="currentColor" />
                            <path opacity="0.3" d="M17 4H20L22 10H18L17 4Z" fill="currentColor" />
                            <path opacity="0.3" d="M7 4H4L2 10H6L7 4Z" fill="currentColor" />
                            <path d="M6 10C6 11.1 5.1 12 4 12C2.9 12 2 11.1 2 10H6ZM10 10C10 11.1 10.9 12 12 12C13.1 12 14 11.1 14 10H10ZM18 10C18 11.1 18.9 12 20 12C21.1 12 22 11.1 22 10H18ZM19 2H5C4.4 2 4 2.4 4 3V4H20V3C20 2.4 19.6 2 19 2ZM12 17C12 16.4 11.6 16 11 16H6C5.4 16 5 16.4 5 17C5 17.6 5.4 18 6 18H11C11.6 18 12 17.6 12 17Z" fill="currentColor" />
                        </svg>
                    </span>
                    Change Password</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="kt_ecommerce_settings_general" role="tabpanel">
                    <div class="card card-flush">
                        <div class="card-body pt-10" >
                            <form id="kt_ecommerce_general_form" method="POST" class="form" enctype="multipart/form-data" autocomplete="off">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('add_on_script')

<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
<script>
    var tabs = '{{ $tab }}';
    getTab(tabs);
    function getTab(tabType) {

        if( tabType == "myaccount")
        {
            $('.myaccount').addClass('active');
            $('.password').removeClass('active');
        } else {
            $('.password').addClass('active');
            $('.myaccount').removeClass('active');
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('my-profile.get.tab') }}",
            type: 'POST',
            data: {tabType:tabType},
            success: function(res) {
                if( tabType == 'password' ) {
                    var updateUrl = "{{ route('my-profile.password') }}";
                } else {
                    var updateUrl = "{{ route('my-profile') }}";
                }
                window.history.pushState("object or string", "Title", updateUrl);

                $('#kt_ecommerce_general_form').html( res );
            }
        });
    }

    

   
</script>
  
@endsection