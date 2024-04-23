<!--begin::Header-->
<div class="card-header" id="kt_activities_header">
    <h3 class="card-title fw-bolder text-dark">{{ $modal_title ?? 'Form Action' }}</h3>
    <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
            <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                        transform="rotate(-45 6 17.3137)" fill="currentColor" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                        transform="rotate(45 7.41422 6)" fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </button>
    </div>
</div>
<!--end::Header-->
<!--begin::Body-->
<form id="add_product_attribute_form" class="form" action="#" enctype="multipart/form-data">

    <div class="card-body position-relative" id="kt_activities_body">
        <div id="kt_activities_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
            data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
            <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll">
                <div class="fv-row mb-10">
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                        data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header"
                        data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">

                        <input type="hidden" name="id" value="{{ $info->id ?? '' }}">
                        <input type="hidden" name="from" id="from" value="{{ $from ?? '' }}">


                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Title</label>
                            <input type="text" name="title" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Title" value="{{ $info->title ?? '' }}" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class=" fw-bold fs-6 mb-2">Tagline</label>
                            <input type="text" name="tag_line" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Tag Line" value="{{ $info->tag_line ?? '' }}" />
                        </div>

                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Product Category</label>
                            @if (isset($category_info) && !empty($category_info))
                                <input type="hidden" name="product_category_id" id="product_category_id"
                                    value="{{ $category_info->id }}">
                                <p>{{ $category_info->name ?? '' }}</p>
                            @else
                                <select name="product_category_id" id="product_category_id"
                                    aria-label="Select a Category" data-control="select2"
                                    data-placeholder="Select a Category..." class="form-select mb-2">
                                    <option value="">-select--</option>
                                    @if (isset($sub_category) && !empty($sub_category))
                                        @foreach ($sub_category as $item)
                                            <option value="{{ $item->id }}"
                                                @if (isset($info->product_category_id) && $info->product_category_id == $item->id) selected @endif>{{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            @endif

                        </div>

                        <br>
                        <div class="row mb-7">
                            <div class="col-md-4">
                                <div class="mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Searchable </label>
                                    <div
                                        class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_searchable"
                                            value="1" @if (isset($info->is_searchable) && $info->is_searchable == '1') checked @endif />
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Comparable </label>
                                    <div
                                        class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_comparable"
                                            value="1" @if (isset($info->is_comparable) && $info->is_comparable == '1') checked @endif />
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-4">
                                <div class="mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Use in Product Overview </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_use_in_product_listing" value="1"
                                            @if (isset($info->is_use_in_product_listing) && $info->is_use_in_product_listing == '1') checked @endif />
                                    </div>
                                </div>
                            </div> --}}
                        </div>

                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Sorting Order</label>
                            <input type="text" name="order_by"
                                class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                placeholder="Sorting Order" value="{{ $info->order_by ?? '' }}" />
                        </div>

                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2"> Status </label>
                            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                <input class="form-check-input" type="checkbox" name="status" value="1"
                                    @if (isset($info->status) && $info->status == 'published') checked @endif />
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-5 text-center" id="kt_activities_footer">
        <div class="text-end px-8">
            <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
            <button type="submit" class="btn btn-primary" data-kt-order_status-modal-action="submit">
                <span class="indicator-label">Submit</span>
                <span class="indicator-progress">Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>
</form>

<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<script>
    $('.mobile_num').keypress(
        function(event) {
            if (event.keyCode == 46 || event.keyCode == 8) {
                //do nothing
            } else {
                if (event.keyCode < 48 || event.keyCode > 57) {
                    event.preventDefault();
                }
            }
        }
    );

    $('#product_category_id').select2();
    var add_url = "{{ route('product-attribute.save') }}";

    // Class definition
    var KTUsersAddRole = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_product_attribute_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);


        // Init add schedule modal
        var initAddRole = () => {

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'title': {
                            validators: {
                                notEmpty: {
                                    message: 'Title is required'
                                }
                            }
                        },
                        'product_category_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Product Category is required'
                                }
                            }
                        },
                    },

                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.fv-row',
                            eleInvalidClass: '',
                            eleValidClass: ''
                        })
                    }
                }
            );

            // Cancel button handler
            const cancelButton = element.querySelector('#discard');
            cancelButton.addEventListener('click', e => {
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
                        commonDrawer.hide(); // Hide modal				
                    }
                });
            });

            // Submit button handler
            const submitButton = element.querySelector('[data-kt-order_status-modal-action="submit"]');
            // submitButton.addEventListener('click', function(e) {
            $('#add_product_attribute_form').submit(function(e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {

                            var formData = new FormData(document.getElementById(
                                "add_product_attribute_form"));
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            // Disable button to avoid multiple click 
                            submitButton.disabled = true;
                            var from = $('#from').val();
                            //call ajax call
                            $.ajax({
                                url: add_url,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                beforeSend: function() {},
                                success: function(res) {
                                    console.log(res.views);
                                    if (res.error == 1) {
                                        // Remove loading indication
                                        submitButton.removeAttribute(
                                            'data-kt-indicator');
                                        // Enable button
                                        submitButton.disabled = false;
                                        let error_msg = res.message
                                        Swal.fire({
                                            text: res.message,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        });
                                    } else {
                                        if (!from) {
                                            dtTable.ajax.reload();
                                        }

                                        Swal.fire({
                                            text: res.message,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then(function(result) {
                                            if (result
                                                .isConfirmed) {
                                                commonDrawer.hide();
                                                if (res.from) {}

                                            }
                                        });
                                    }
                                }
                            });

                        } else {
                            // Show popup warning. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                text: "Sorry, looks like there are some errors detected, please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                }
            });

        }

        return {
            // Public functions
            init: function() {
                initAddRole();
            }
        };
    }();

    // On document ready
    KTUtil.onDOMContentLoaded(function() {
        KTUsersAddRole.init();
    });
</script>
