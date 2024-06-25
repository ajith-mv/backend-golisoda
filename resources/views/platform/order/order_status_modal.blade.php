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
<form id="change_order_status_form" class="form" action="#" enctype="multipart/form-data">

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
                      <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2"> Order Id </label>
                                   <input type="text" class="form-control"  value="{{ $info->id ?? '' }}" readonly>
                                </div>
                            </div>
                        </div>
                          <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2"> Order No </label>
                                    <input type="text" class="form-control"  value="{{ $info->order_no ?? '' }}" readonly>
                                </div>
                            </div>
                        </div>
                      
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Order Status</label>
                                    <select class="form-control" name="order_status_id" id="order_status_id">
                                        <option value="">--select--</option>
                                        @if (isset($order_status_info) && !empty($order_status_info))
                                            @foreach ($order_status_info as $item)
                                                <option value="{{ $item->id }}"
                                                    @if ($info->order_status_id == $item->id) selected @endif>
                                                    {{ $item->status_name }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2"> Description </label>
                                    <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Description" name="description"
                                        id="description" cols="30" rows="5">{{ $info->description ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <!--<div class="row d-none" id="delivery-document">-->
                        <!--    <div class="col-md-12">-->
                        <!--        <div class="fv-row mb-7">-->
                        <!--            <label class="required fw-bold fs-6 mb-2"> OTP </label>-->
                        <!--            <input type="text" name="otp" id="otp"-->
                        <!--                class="form-control">-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--    <div class="col-md-12">-->
                        <!--        <div class="fv-row mb-7">-->
                        <!--            <label class="required fw-bold fs-6 mb-2"> Delivery Document </label>-->
                        <!--            <input type="file" name="delivery_document" id="delivery_document"-->
                        <!--                class="form-control">-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        @include('platform.order.tracking_form')

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-5 text-center" id="kt_activities_footer">
        <div class="text-end px-8">
            <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
            <button type="submit" class="btn btn-primary" data-kt-customer-modal-action="submit">
                <span class="indicator-label">Submit</span>
                <span class="indicator-progress">Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>
</form>

<script>
    var formValidateFields = {
        'order_status_id': {
            validators: {
                notEmpty: {
                    message: 'Order Status is required'
                }
            }
        },
        'description': {
            validators: {
                notEmpty: {
                    message: 'Description is required'
                }
            }
        },

    };
    var order_status_id = document.getElementById('order_status_id');

    order_status_id.addEventListener('change', function() {

        console.log($("#order_status_id option:selected").text());
        if ($("#order_status_id option:selected").text().trim() == 'Order Delivered') {
            console.log('inside');
            $('#delivery-document').removeClass('d-none');
            formValidateFields = {
                'order_status_id': {
                    validators: {
                        notEmpty: {
                            message: 'Order Status is required'
                        }
                    }
                },

                'description': {
                    validators: {
                        notEmpty: {
                            message: 'Description is required'
                        }
                    }
                },
                
                
            };
        } else {
            $('#delivery-document').addClass('d-none');
            formValidateFields = {
                'order_status_id': {
                    validators: {
                        notEmpty: {
                            message: 'Order Status is required'
                        }
                    }
                },
                'description': {
                    validators: {
                        notEmpty: {
                            message: 'Description is required'
                        }
                    }
                },

            };
        }

    })


    var add_url = "{{ route('order.change.status') }}";

    // Class definition
    var KTUsersAddRole = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#change_order_status_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);


        // Init add schedule modal
        var initAddRole = () => {

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/


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
            const submitButton = element.querySelector('[data-kt-customer-modal-action="submit"]');
            // submitButton.addEventListener('click', function(e) {
            $('#change_order_status_form').submit(function(e) {
                var validator = FormValidation.formValidation(
                    form, {
                        fields: formValidateFields,

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
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {

                            var formData = new FormData(document.getElementById(
                                "change_order_status_form"));
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            // Disable button to avoid multiple click 
                            submitButton.disabled = true;

                            //call ajax call
                            $.ajax({
                                url: add_url,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                beforeSend: function() {},
                                success: function(res) {
                                    if (res.error == 1) {
                                        // Remove loading indication
                                        submitButton.removeAttribute('data-kt-indicator');
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
                                        window.location.reload();
                                    } else {
                                        dtTable.ajax.reload();
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
                                                commonDrawer
                                                    .hide();

                                            }
                                        });
                                        window.location.reload();
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
