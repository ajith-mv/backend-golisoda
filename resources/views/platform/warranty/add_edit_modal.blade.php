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
<form id="add_warranty_form" class="form" action="#" enctype="multipart/form-data">

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
                      
                      
                        <div class="fv-row mb-7">

                            <div class="row">
                                    <label class="required fw-bold fs-6 mb-2">Warranty Name</label>
                                    <input type="text" name="name" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Name" value="{{ $info->name ?? '' }}" />
                            <input type="hidden" name="from" id="from" value="{{ $from ?? '' }}">
                            </div>
                        </div>
                       
                        <div class="fv-row">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                            <label class="required fw-bold fs-6 mb-2">Warranty Period</label>
                                            <input type="text" name="warranty_period" id="warranty_period" class="form-control form-control-solid mb-3 mb-lg-0 numberonly"
                                                placeholder="Warranty Period" value="{{ $info->warranty_period ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="required fw-bold fs-6 mb-2">Warranty period type</label>
                                    <select name="warranty_period_type" id="warranty_period_type"
                                            aria-label="Select a type" data-control="select2"
                                            data-placeholder="Select a type..." class="form-select mb-2">
                                            <option value="">--Select--</option>
                                            <option value="Day" @if((isset($info->warranty_period_type) && $info->warranty_period_type == 'Day' )) selected @endif > Day</option>
                                            <option value="Month"  @if((isset($info->warranty_period_type) && $info->warranty_period_type == 'Month' )) selected @endif>Month</option>
                                            <option value="Year"  @if((isset($info->warranty_period_type) && $info->warranty_period_type == 'Year' )) selected @endif>Year</option>
                                    </select>
                                </div>
                              
                            </div>
                        </div>
                        <div class="fv-row mb-7">

                                <label class="fw-bold fs-6 mb-2">Warranty Information</label>
                                <textarea name="description" id="description" class="form-control form-control-solid mb-3 mb-lg-0" cols="30" rows="2">{{ $info->description ?? '' }}</textarea>
                        
                        </div>
                            <div class="fv-row mb-7">
                                <div class="row">
                                        <div class="col-md-6">
                                                <label class="fw-bold fs-6 mb-2">Sorting Order</label>
                                                <input type="text" name="order_by" class="form-control form-control-solid mb-3 mb-lg-0 numberonly"
                                                placeholder="Sorting Order" value="{{ $info->order_by ?? '' }}" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-bold fs-6 mb-2"> Status </label>
                                            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                                <input class="form-check-input" type="checkbox"  name="status" value="1"  @if( ( isset( $info->status) && $info->status == 'published') || (!isset( $info->status ))) checked @endif />
                                            </div>
                                        </div>
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
      document.getElementById('readUrl').addEventListener('change', function() {
      
        if (this.files[0]) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
                console.log(event.target);
                let img_url = event.target.result;
                $('#manual-image').css({
                    'background-image': 'url(' + event.target.result + ')'
                });
            });
        }
    });
    document.getElementById('avatar_remove_logo').addEventListener('click', function() {
        $('#image_remove_image').val("yes");
        $('#manual-image').css({
            'background-image': ''
        });
    });
 
</script>
<script>
 $(document).ready(function () {    
        $('.numberonly').keypress(function (e) {    
            var charCode = (e.which) ? e.which : event.keyCode    
            if (String.fromCharCode(charCode).match(/[^0-9]/g))    
                return false;                        
        });    

    }); 
  
    var add_url = "{{ route('warranty.save') }}";
$('#warranty_period_type').select2();
    // Class definition
    var KTUsersAddProductWarranty = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_warranty_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);

        // Init add schedule modal
        var initAddProductWarranty = () => {

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'name': {
                            validators: {
                                notEmpty: {
                                    message: 'Name is required'
                                }
                            }
                        },

                        'warranty_period_type': {
                            validators: {
                                notEmpty: {
                                    message: 'Warranty Type is required'
                                }
                            }
                        },

                        'warranty_period': {
                            validators: {
                                notEmpty: {
                                    message: 'Warranty Period is required'
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
            $('#add_warranty_form').submit(function(e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {
                            var from = $('#from').val();
                            var formData = new FormData(document.getElementById("add_warranty_form"));
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
                                beforeSend: function() {
                                    
                                },
                                success: function(res) {
                                    submitButton.disabled = false;
                                    if (res.error == 1) {
                                        // Remove loading indication
                                     
                                        // Enable button
                                       
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
                                        if( from ) {
                                            getProductWarrantyDropdown(res.id);
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
                initAddProductWarranty();
            }
        };
    }();

    // On document ready
    KTUtil.onDOMContentLoaded(function() {
        KTUsersAddProductWarranty.init();
    });

   
</script>
