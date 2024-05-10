@extends('platform.layouts.template')
@section('toolbar')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            @include('platform.layouts.parts._breadcrum')
        </div>
    </div>
    <style>
        label.error {
            color: red;
        }

        .dropzone .dz-preview.lp-preview {
            width: 150px !important;
        }

        .dropzone {
            width: 740px !important;
            overflow: auto !important;
            white-space: nowrap !important;
        }
    </style>
@endsection
@section('content')
    <div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl px-2">
                <form id="kt_ecommerce_add_product_form" method="POST" class="form d-flex flex-column flex-lg-row">
                    @csrf
                    <input type="hidden" name="id" value="{{ $info->id ?? '' }}">
                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-250px mb-7 me-lg-3">
                        @include('platform.product.form.parts._common_side')
                    </div>

                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                        <!--begin:::Tabs-->
                        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-n2">
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4 active" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_general">General</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_description">Descriptions</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_filter">Filter</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_variation">Variation</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_meta">Meta Tags</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_related">Related Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_url">URL</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general" role="tab-panel">
                                @include('platform.product.form.general.general')
                            </div>

                            <div class="tab-pane fade" id="kt_ecommerce_add_product_description" role="tab-panel">
                                @include('platform.product.form.description.description')
                            </div>

                            <div class="tab-pane fade" id="kt_ecommerce_add_product_filter" role="tab-panel">
                                @include('platform.product.form.filter.filter')
                            </div>
                            <div class="tab-pane fade" id="kt_ecommerce_add_product_variation" role="tab-panel">
                                @include('platform.product.form.variation.variation')
                            </div>
                            <div class="tab-pane fade" id="kt_ecommerce_add_product_meta" role="tab-panel">
                                @include('platform.product.form.meta.meta')
                            </div>

                            <div class="tab-pane fade" id="kt_ecommerce_add_product_related" role="tab-panel">
                                @include('platform.product.form.related.related')
                            </div>
                            <div class="tab-pane fade" id="kt_ecommerce_add_product_url" role="tab-panel">
                                @include('platform.product.form.url.url')
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="javascript:void(0);" id="kt_ecommerce_add_product_cancel"
                                class="btn btn-light me-5">Cancel</a>

                            <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                                <span class="indicator-label">Save Changes</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('add_on_script')
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('#kt_ecommerce_add_product_form').validate({
                rules: {
                    product_name: "required",
                    sku: "required",
                    category_id: "required",
                    brand_id: "required",
                    base_price: "required",
                },
                messages: {
                    product_name: "Product Name is required",
                    sku: "Product Sku is required",
                    category_id: "Category is required",
                    brand_id: "Brand is required",
                    base_price: "Base is required",
                },
                submitHandler: function(form) {

                    var action = "{{ route('products.save') }}";
                    var forms = $('#kt_ecommerce_add_product_form')[0];
                    var formData = new FormData(forms);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('products.save') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function() {
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            submitButton.disabled = true;
                        },
                        success: function(res) {
                            if (res.error == 1) {
                                // Remove loading indication
                                submitButton.removeAttribute('data-kt-indicator');
                                // Enable button
                                submitButton.disabled = false;
                                let error_msg = res.message
                                Swal.fire({
                                    html: res.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            } else {

                                if (res.product_id) {
                                    // updateFormData();
                                    setTimeout(() => {
                                        myDropzone.processQueue();
                                    }, 500);
                                    //     myDropzone.on("addedfiles", (file) => {
                                    //     //    console.log( myDropzone.hiddenFileInput );
                                    //    });
                                }

                                submitButton.removeAttribute('data-kt-indicator');
                                // Enable button
                                submitButton.disabled = false;
                                var fileInput = document.getElementById("gallery_image");
                                fileInput.value = "";
                                setTimeout(() => {
                                    if (res.isUpdate) {
                                        setTimeout(() => {
                                            location.reload();
                                        }, 2000);
                                    } else {
                                        window.location.href = product_url;
                                    }
                                }, 500);
                                Swal.fire({
                                    // text: "Thank you! You've updated Products",
                                    text: res.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function(result) {
                                    if (result.isConfirmed) {

                                        // window.location.href=product_url;

                                    }
                                });
                            }
                        }
                    });
                }
            });


            $('#related_product').select2();
            $('#cross_selling_product').select2();
            $("body").on("click", ".removeUrlRow", function() {
                $(this).parents(".childUrlRow").remove();
            })
        });

        // alert();
        @if (isset($info->id) && !empty($info->id))
            addVariationRow('{{ $info->id }}');
        @endif
        $('.product-tab').click(function() {

            let types = $(this).attr('href');
            var checkArray = ['#kt_ecommerce_add_product_meta', '#kt_ecommerce_add_product_filter',
                '#kt_ecommerce_add_product_related'
            ];
            if (checkArray.includes(types)) {
                console.log('welcome');
            } else {
                return true;
            }

        });


        var isImage = false;
        var product_url = "{{ route('products') }}";
        var product_add_url = "{{ route('products.save') }}";
        var remove_image_url = "{{ route('products.remove.image') }}";
        var gallery_upload_url = "{{ route('products.upload.gallery') }}";




        function removeGalleryImage(productImageId) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: remove_image_url,
                type: 'POST',
                data: {
                    id: productImageId
                },
                success: function(res) {
                    console.log(res);
                }
            });

        }
        $(document).on('change', '#category_id', function(e) {
            var category_id = $(this).val();
            var id = '';
            addProductVariationRow(id, category_id);
        });

        // $(document).on('change', 'select[name="kt_docs_repeater_nested_outer[0][select2_input]"]', function(e) {
        //     var length = $('#kt_docs_repeater_nested').length;
        //     console.log(length);
        //     var variation = $(this).val();
        //     var currentRepeaterItem = $(this).closest('[data-repeater-item]');
        //     var variationValueSelect = currentRepeaterItem.find(
        //         'select[name="kt_docs_repeater_nested_outer[0][kt_docs_repeater_nested_inner][0][variation_value]"]'
        //         );
        //     console.log(currentRepeaterItem);
        //     $.ajax({
        //         url: "{{ route('products.variation.value') }}",
        //         type: "GET",
        //         data: {
        //             variation: variation,
        //         },
        //         success: function(res) {
        //             if (res.values) {
        //                 $.each(res.values, function(key, data) {
        //                     variationValueSelect.append('<option value="' + data.value + '">' +
        //                         data.value + '</option>');
        //                 });
        //             }
        //         },
        //         error: function(error) {
        //             console.log('Error fetching variation_value:', error);
        //         }
        //     });
        // });



        @if (isset($info->id) && !empty($info->id))
            var info_id = '{{ $info->id }}';
            addProductVariationRow(info_id, category_id);
        @endif

        function addProductVariationRow(id, category_id) {
            var category_id = $('#category_id').val();
            console.log(category_id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('products.variation.row') }}",
                type: "POST",
                data: {
                    product_id: id,
                    category_id: category_id
                },
                success: function(res) {
                    $('#formRepeaterIds').append(res);
                   $('#title').remove(); 
                },
                error: function(error) {
                    toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-top-left",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };
                    toastr.error(error.responseJSON.message);
                }
            });
        }


        function addVariationRow(id = '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('products.attribute.row') }}",
                type: "POST",
                data: {
                    product_id: id
                },
                success: function(res) {
                    $('#formRepeaterId').append(res);
                }

            });
        }

        function removeRow(data) {
            $(data).parents(".childRow").remove();
        }

        var productCancelButton;
        productCancelButton = document.querySelector('#kt_ecommerce_add_product_cancel');
        productCancelButton.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                text: "Are you sure you would like to cancel?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, cancel it!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function(result) {
                if (result.value) {
                    window.location.href = product_url
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Your form has not been cancelled!.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        }
                    });
                }
            });
        });

        function addLinks() {
            var addRow = $('#child-url').clone();
            $("#child-url").clone().appendTo("#formRepeaterUrl").find("input[type='text']").val("");
        }

        $('.numberonly').keypress(function(e) {
            var charCode = (e.which) ? e.which : event.keyCode
            if (String.fromCharCode(charCode).match(/[^0-9]/g))
                return false;
        });
    </script>
    <script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apps/ecommerce/catalog/save-product.js') }}"></script>
@endsection
