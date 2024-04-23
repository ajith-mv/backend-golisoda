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

<div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-0 align-items-center">
                <li class="nav-item">
                    <a class="nav-link text-active-primary active" data-bs-toggle="tab" href="#kt_ecommerce_add_service_center_general">General</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary" data-bs-toggle="tab" href="#kt_ecommerce_add_service_center_meta">Meta</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <form id="add_service_center_form" class="form" enctype="multipart/form-data">
                <div id="kt_activities_body">
                    <div id="kt_activities_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true"
                        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
                        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
                        <div class="d-flex flex-column scroll-y me-n7 pe-7 " id="kt_modal_update_role_scroll">
                            <div class="fv-row mb-1">
                                <div class="d-flex flex-column scroll-y me-n7 pe-7 py-4" id="kt_modal_add_user_scroll"
                                    data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                                    data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header"
                                    data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                                    <input type="hidden" name="id" value="{{ $info->id ?? '' }}">
                                    <input type="hidden" name="from" id="from" value="{{ $from ?? '' }}">
        
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="kt_ecommerce_add_service_center_general" role="tab-panel">
                                            @include('platform.service_center.form._service_centr_form')
                                        </div>
                                        <div class="tab-pane fade" id="kt_ecommerce_add_service_center_meta" role="tab-panel">
                                            @include('platform.common.form._meta_form')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-5 text-center" id="kt_activities_footer">
                        <div class="text-end px-8">
                            <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
                            <button type="submit" class="btn btn-primary" data-kt-service_center-modal-action="submit">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div> 
</div>
<script>

    $('.numberonly').keypress(function (e) {    
        var charCode = (e.which) ? e.which : event.keyCode    
        if (String.fromCharCode(charCode).match(/[^0-9]/g))    
            return false;                        
    });  

    $('#is_parent').change(function(){
        if($("#is_parent").prop('checked') == true){
            $('#parent-tab').addClass('d-none');
        } else {
            $('#parent-tab').removeClass('d-none');
        }
    });

    $('#is_tax').change(function(){
        if($("#is_tax").prop('checked') == true){
            $('#tax-tab').removeClass('d-none');
        } else {
            $('#tax-tab').addClass('d-none');
        }
    });
   
    //image image script
     document.getElementById('bannerUrl').addEventListener('change', function() {
        if (this.files[0]) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
                console.log(event.target);
                let img_url = event.target.result;
                $('#banner-image').css({
                    'background-image': 'url(' + event.target.result + ')'
                });
            });
        }
    });
    document.getElementById('banner_remove_logo').addEventListener('click', function() {
        $('#banner_remove_image').val("yes");
        $('#banner-image').css({
            'background-image': ''
        });
    });

    // banner mobile image

    document.getElementById('bannerMobileUrl').addEventListener('change', function() {
        // console.log("111");
        if (this.files[0]) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
                console.log(event.target);
                let img_url = event.target.result;
                $('#banner-mobile-image').css({
                    'background-image': 'url(' + event.target.result + ')'
                });
            });
        }
    });
    document.getElementById('banner_mobile_remove_logo').addEventListener('click', function() {
        $('#banner_mobile_remove_image').val("yes");
        $('#banner-mobile-image').css({
            'background-image': ''
        });
    });
   
    



    $('#parent_location').select2();
    var add_url = "{{ route('service-center.save') }}";
    // Class definition
    var KTProductCategory = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_service_center_form');
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
                        'pincode': {
                            validators: {
                                notEmpty: {
                                    message: 'Pincode is required'
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
                        'whatsapp_no': {
                            validators: {
                                notEmpty: {
                                    message: 'Whatsapp No is required'
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
            const submitButton = element.querySelector('[data-kt-service_center-modal-action="submit"]');
            // submitButton.addEventListener('click', function(e) {
            submitButton.addEventListener('click', function (e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        
                        if (status == 'Valid') {
                            var from = $('#from').val();
                            var form = $('#add_service_center_form')[0]; 
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
                                        if( from != '' ) {
                                            getProductCategoryDropdown(res.categoryId);
                                            return false;
                                        }
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
                initAddRole();
            }
        };
    }();
    // On document ready
    KTUtil.onDOMContentLoaded(function() {
        KTProductCategory.init();
    });

   
</script>
