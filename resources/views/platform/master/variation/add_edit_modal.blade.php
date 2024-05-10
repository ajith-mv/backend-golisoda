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
<form id="add_variation_form" class="form" action="#" enctype="multipart/form-data">

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

                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Title </label>
                            <input type="text" name="title" value="{{ $info['title'] ?? '' }}"
                                class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Variation Title" />
                        </div>
                        <input type="hidden" name="id" value="{{ $info['id'] ?? '' }}">


<!--begin::Repeater-->
{{-- <div id="kt_docs_repeater_nested">
    <!--begin::Form group-->
    <div class="form-group">
        <div data-repeater-list="kt_docs_repeater_nested_outer">
               @php 
                $data =json_decode($info != '' ? $info->value : '', true);
                @endphp
                @if(isset($data))
              @foreach($data as $key)
            <div data-repeater-item class="form-group row mb-5">
                <div class="col-md-3 ">
                    <label class="form-label">Value:</label>
                    <input type="text" name="value" value="{{ $key['value'] }}" class="form-control mb-2 mb-md-0" placeholder="Enter value" />
                </div>
                <div class="col-md-4 d-flex align-items-center mt-5">
                    <a href="javascript:;" data-repeater-delete class="btn btn-sm btn-light-danger mt-3 mt-md-0">
                        <i class="la la-trash-o fs-3"></i>Delete Row
                    </a>
                </div>
            </div>
            @endforeach
            @else
            <div data-repeater-item class="form-group row mb-5">
                <div class="col-md-3">
                    <label class="form-label">Value:</label>
                    <input type="text" name="value" value="" class="form-control mb-2 mb-md-0" placeholder="Enter value" />
                </div>
                <div class="col-md-4 d-flex align-items-center mt-5">
                    <a href="javascript:;" data-repeater-delete class="btn btn-sm btn-light-danger mt-3 mt-md-0">
                        <i class="la la-trash-o fs-3"></i>Delete Row
                    </a>
                </div>
            </div>
          @endif
        </div>
    </div>
    <div class="form-group">
        <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
            <i class="la la-plus"></i>Add Value
        </a>
    </div>
</div> --}}

<!--end::Repeater-->


                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Tag Line</label>
                            <input type="text" name="tag_line"  class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Tag Line" value="{{ $info['tag_line'] ?? '' }}" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Sort</label>
                            <input type="text" name="sort"  class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Sort" value="{{ $info['sort'] ?? '' }}" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2"> Status </label>
                            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                <input class="form-check-input" type="checkbox"  name="status" value="1"  @if(isset( $info['status']) && $info['status'] == '1') checked @endif />
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
    
    $( document ).ready(function() {
$('#kt_docs_repeater_nested').repeater({
    show: function () {
        $(this).slideDown();
    },

    hide: function (deleteElement) {
        $(this).slideUp(deleteElement);
    }
});
});

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
    var add_url = "{{ route('variation.save') }}";

    // Class definition
    var KTUsersAddRole = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_variation_form');
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
                                    message: 'Variation title is required'
                                }
                            }
                        },
                        'tag_line': {
                            validators: {
                                notEmpty: {
                                    message: 'Tag Line is required'
                                }
                            }
                        },
                        'value': {
                            validators: {
                                notEmpty: {
                                    message: 'value is required'
                                }
                            }
                        },
                        
                        'sort': {
                            validators: {
                                notEmpty: {
                                    message: 'Sort is required'
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
            $('#add_variation_form').submit(function(e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {

                            var formData = new FormData(document.getElementById(
                                "add_variation_form"));
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

        // Select all handler
        const handleSelectAll = () => {
            // Define variables
            const selectAll = form.querySelector('#kt_order_stautsorder_status_select_all');
            const allCheckboxes = form.querySelectorAll('[type="checkbox"]');

            // Handle check state
            selectAll.addEventListener('change', e => {
                // Apply check state to all checkboxes
                allCheckboxes.forEach(c => {
                    c.checked = e.target.checked;
                });
            });

        }


        return {
            // Public functions
            init: function() {
                initAddRole();
                handleSelectAll();
            }
        };
    }();

    // On document ready

    KTUtil.onDOMContentLoaded(function() {
        KTUsersAddRole.init();
    });

    $('.common-checkbox').click(function() {
        $("#kt_order_stauts_select_all").prop("checked", false);
    });
</script>
