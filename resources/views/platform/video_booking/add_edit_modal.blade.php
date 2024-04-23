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
<form id="add_video_booking_form" class="form" action="#" enctype="multipart/form-data">

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
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-bold fs-6 mb-2">Customer</label>
                                    <select name="customer_id" id="customer_id" aria-label="Select a Customer" class="form-select form-select-solid fw-bolder">
                                        <option value="">Select a Customer...</option>
                                        @foreach($customer as $key=>$val)
                                        <option value="{{ $val->id }}" @if(isset( $info->customer_id) && $info->customer_id == $val->id) selected @endif >{{ $val->first_name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-bold fs-6 mb-2">Contact Name</label>
                                <input type="text" name="contact_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                    placeholder="Contact Name" value="{{ $info->contact_name ?? '' }}" />
                            </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-bold fs-6 mb-2">Contact Email</label>
                                <input type="text" name="contact_email" class="form-control form-control-solid mb-3 mb-lg-0 "
                                placeholder="Contact Email" value="{{ $info->contact_email ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-bold fs-6 mb-2">Contact Phone</label>
                                <input type="text" name="contact_phone" minlength="10" maxlength="10" class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                    placeholder="Contact Phone" value="{{ $info->contact_phone ?? '' }}" />
                            </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class=" required fw-bold fs-6 mb-2">Product Name</label>
                                <select name="product_id" id="product_id" aria-label="Select a Product" class="form-select form-select-solid fw-bolder">
                                    <option value="">Select a Product...</option>
                                    @foreach($product as $key=>$val)
                                    <option value="{{ $val->id }}" @if(isset( $info->product_id) && $info->product_id == $val->id) selected @endif >{{ $val->product_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-bold fs-6 mb-2">Reach Type</label>
                                <input type="text" name="reach_type" class="form-control form-control-solid mb-3 mb-lg-0"
                                    placeholder="Reach Type" value="{{ $info->reach_type ?? '' }}" />
                            </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class=" required fw-bold fs-6 mb-2">Preferred Date</label>
                                <input type="text" name="preferred_date" id="preferred_date" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Preferred Date" value="{{ $info->preferred_date ?? '' }}"  />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row mb-7">
                                <label class="required fw-bold fs-6 mb-2">Preferred Time</label>
                                <input type="text" name="preferred_time" id="preferred_time" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Preferred Time"  value="{{ $info->preferred_time ?? '' }}"  />
                            </div>
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
    var add_url = "{{ route('video-booking.save') }}";
   
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
    $('#customer_id').select2();
    $('#product_id').select2();
    document.getElementById("preferred_date").flatpickr({
            enableTime: false,
			dateFormat: "Y-m-d",
        });
        document.getElementById("preferred_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_12hr: true
        });
        
    var KTUsersTax = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_video_booking_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);
        var initTax = () => {
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'customer_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Customer Name is required'
                                }
                            }
                        },
                        'contact_name': {
                            validators: {
                                notEmpty: {
                                    message: 'Contact Name is required'
                                }
                            }
                        },
                        'contact_email': {
                            validators: {
                                notEmpty: {
                                    message: 'Email is required'
                                },
                                emailAddress: {
                                    message: 'The value is not a valid email address',
                                }
                            }
                        },
                        'contact_phone': {
                            validators: {
                                notEmpty: {
                                    message: 'Contact Phone is required'
                                }
                            }
                        },
                        // 'product_id': {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Product Name is required'
                        //         }
                        //     }
                        // },
                        'reach_type': {
                            validators: {
                                notEmpty: {
                                    message: 'Reach Type is required'
                                }
                            }
                        },
                        'preferred_date': {
                            validators: {
                                notEmpty: {
                                    message: 'Preferred Date is required'
                                }
                            }
                        },

                        'preferred_time': {
                            validators: {
                                notEmpty: {
                                    message: 'Preferred Time is required'
                                }
                            }
                        },
                        
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.fv-row',
                            eleInvalidClass: '',
                            eleValidClass: ''
                        }),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'fa fa-check',
                            invalid: 'fa fa-times',
                            validating: 'fa fa-refresh',
                        }),
                    }
                }
            );
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
            const submitButton = element.querySelector('[data-kt-order_status-modal-action="submit"]');
            submitButton.addEventListener('click', function (e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        
                        if (status == 'Valid') {
                           
                            var form = $('#add_video_booking_form')[0]; 
                            var formData = new FormData(form);
                            submitButton.setAttribute('data-kt-indicator', 'on');
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
            init: function() {
                initTax();
            }
        };


    }();
    KTUtil.onDOMContentLoaded(function() {
        KTUsersTax.init();
    });

</script>