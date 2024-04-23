@extends('auth.layouts.template')
@section('content')
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-xl-row flex-column-fluid">
            <div class="d-flex flex-column flex-center flex-lg-row-fluid">
                @include('auth._left_pane')
            </div>

            <div class="flex-row-fluid d-flex flex-center justfiy-content-xl-first p-10">
                <div class="d-flex flex-center p-15 shadow-sm bg-body rounded w-100 w-md-550px mx-auto ms-xl-20">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate"
                        id="kt_password_reset_form" action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <!--begin::Heading-->
                        <div class="text-center mb-10">
                            <!--begin::Title-->
                            <h1 class="text-dark mb-3">Forgot Password ?</h1>
                            <!--end::Title-->
                            <!--begin::Link-->
                            <div class="text-gray-400 fw-bold fs-4">Enter your email to reset your password.</div>
                            <!--end::Link-->
                        </div>
                        <!--begin::Heading-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-10 fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
                            <label class="form-label fw-bolder text-gray-900 fs-6">Email</label>
                            <input class="form-control form-control-solid" type="email" placeholder="" name="email"
                                autocomplete="off">
                            
                        </div>
                        <!--end::Input group-->
                        <!--begin::Actions-->
                        <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                            <button type="button" id="kt_password_reset_submit"
                                class="btn btn-lg btn-primary btn-shadow fw-bolder me-4">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-lg btn-light-primary fw-bolder">Cancel</a>
                        </div>
                        <!--end::Actions-->
                        <div></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('add_on_script')
    <script src="{{ asset('assets/js/custom/authentication/password-reset/password-reset.js') }}"></script>
@endsection
