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
<form id="add_customer_address_form" class="form" action="#" enctype="multipart/form-data">

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
                        <input type="hidden" name="customer_id" value="{{ $customerInfo->id ?? '' }}">
                        
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                        <label class="required fw-bold fs-6 mb-2">Address Type</label>
                                            <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select an option" id="address_type_id" name="address_type_id">
                                                @isset($addressType->subCategory)
                                                    @foreach ($addressType->subCategory as $key=>$val )
                                                        <option value="{{ $val->id }}" @if(isset($info->address_type_id) && $info->address_type_id == $val->id) selected @endif>{{ $val->name }}</option>
                                                    @endforeach 
                                                @endisset
                                              
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                        <label class="required fw-bold fs-6 mb-2">First Name</label>
                                        <input type="text" name="first_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                            placeholder="First Name" value="{{ $info->name ?? '' }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                        <label class="required fw-bold fs-6 mb-2">Email</label>
                                        <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0"
                                            placeholder="Email" value="{{ $info->email ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                        <label class="required fw-bold fs-6 mb-2">Mobile Number</label>
                                        <input type="text" name="mobile_no" min="0" maxlength="10" class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                            placeholder="Mobile Number" value="{{ $info->mobile_no ?? '' }}" />
                                    </div>
                                </div>
                            </div>
                    
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2">Address 1</label>
                                        <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Address 1" name="address_line1" id="address_line1" cols="30" rows="3">{{ $info->mobile_no ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2">Address 2</label>
                                        <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Address 2" name="address_line2" id="address_line2" cols="30" rows="3">{{ $info->mobile_no ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2">Landmark</label>
                                    <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Landmark" name="landmark" id="landmark" cols="30" rows="3">{{ $info->landmark ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2">Country</label>
                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select an option" id="country" name="country">
                                        @foreach ($country as $key=>$val )
                                            <option value="{{ $val->id }}"  @if(isset($info->country_id) && $info->country_id == $val->id)selected @endif>{{ $val->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2">State</label>
                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select an option" id="state" name="state">
                                        @foreach ($state as $key=>$val )
                                            <option value="{{ $val->id }}" @if(isset($info->state_id) && $info->state_id == $val->id)selected @endif>{{ $val->state_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2">City</label>
                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select an option" id="city" name="city">
                                        @foreach ($city as $key=>$val ) 
                                            <option value="{{ $val->id }}" @if(isset($info->city_id) && $info->city_id == $val->id)selected @endif>{{ $val->city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2">Post Code</label>
                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select an option" id="post_code" name="post_code">
                                        @foreach ($pinCode as $key=>$val )
                                            <option value="{{ $val->id }}" @if(isset($info->post_code_id) && $info->post_code_id == $val->id)selected @endif>{{ $val->pincode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Default </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox"  name="is_default"   @if((isset( $info->is_default) && $info->is_default == '1' )) checked  @endif />
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                           
                            <div class="col-md-6">
                                <div class="row">
                                    
                                   
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
            <button type="submit" class="btn btn-primary" data-kt-customer_address-modal-action="submit">
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
    $('#address_type_id').select2();
    $('#country').select2();
    $('#state').select2();
    $('#city').select2();
    $('#post_code').select2();
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
    var add_url = "{{ route('customer.address') }}";

    // Class definition
    var KTCustomerAddress = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_customer_address_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);


        // Init add schedule modal
        var initCustomerAddress = () => {

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'address_type_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Address Type is required'
                                }
                            }
                        },
                        'first_name': {
                            validators: {
                                notEmpty: {
                                    message: 'First Name is required'
                                }
                            }
                        },
                        'last_name': {
                            validators: {
                                notEmpty: {
                                    message: 'Last Name is required'
                                }
                            }
                        },
                        'email': {
                            validators: {
                                notEmpty: {
                                    message: 'Email is required'
                                }
                            }
                        },
                        'mobile_no': {
                            validators: {
                                notEmpty: {
                                    message: 'Mobile Number is required'
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
            const submitButton = element.querySelector('[data-kt-customer_address-modal-action="submit"]');
            // submitButton.addEventListener('click', function(e) {
            $('#add_customer_address_form').submit(function(e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {

                            var formData = new FormData(document.getElementById(
                                "add_customer_address_form"));
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

                                                if( res.customer_id ){
                                                    listAddress(res.customer_id);
                                                }

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
                initCustomerAddress();
            }
        };
    }();

    // On document ready

    KTUtil.onDOMContentLoaded(function() {
        KTCustomerAddress.init();
    });

</script>
