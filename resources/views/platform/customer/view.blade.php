@extends('platform.layouts.template')
@section('toolbar')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            @include('platform.layouts.parts._breadcrum')
        </div>
    </div>
@endsection

@section('content')
    <div class="content pt-0 d-flex flex-column flex-column-fluid" id="kt_content">

        <div class="post d-flex flex-column-fluid" id="kt_post">

            <div id="kt_content_container" class="container-xxl">
                <div class="row">
                    <div class="col-sm-12">
                        @include('platform.customer.form.parts._common_side')
                    </div>
                    <div class="col-sm-12">
                        <form id="kt_ecommerce_add_product_form" method="POST" class="form d-flex flex-column flex-lg-row">
                            @csrf


                            <div class="d-flex flex-column flex-row-fluid">
                                <!--begin:::Tabs-->
                                <ul
                                    class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-n2">
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab"
                                            href="#kt_ecommerce_address">Address</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab"
                                            href="#kt_ecommerce_order">Order</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab"
                                            href="#kt_ecommerce_wishlist">Wishlist</a>
                                    </li>

                                </ul>
                                <!--end:::Tabs-->
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="kt_ecommerce_address" role="tab-panel">
                                        <div class="card card-flush">
                                            <div class="card-body">
                                                @include('platform.customer.form.address.address')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="kt_ecommerce_order" role="tab-panel">
                                        <div class="card card-flush">
                                            <div class="card-body">
                                                @include('platform.customer.form.order.order')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="kt_ecommerce_wishlist" role="tab-panel">
                                        <div class="card card-flush">
                                            <div class="card-body">
                                                @include('platform.customer.form.wishlist.wishlist')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Tab content-->
                                <div class="d-flex justify-content-end">
                                </div>
                            </div>
                            <!--end::Main column-->
                        </form>
                    </div>
                </div>
                <!--end::Form-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
@endsection
@section('add_on_script')
    {{-- <script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/custom/apps/ecommerce/catalog/save-product.js') }}"></script> --}}
@endsection
