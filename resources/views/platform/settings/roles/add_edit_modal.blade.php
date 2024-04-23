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
<form id="add_role_form" class="form" action="#">

    <div class="card-body position-relative" id="kt_activities_body">
        <!--begin::Content-->
        <div id="kt_activities_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
            data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
            <!--begin::Scroll-->
            <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll">
                <!--begin::Input group-->
                <div class="fv-row mb-10">
                    <!--begin::Label-->
                    <label class="fs-5 fw-bolder form-label mb-2">
                        <span class="required">Role name</span>
                    </label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="hidden" name="id" value="{{ $info->id ?? '' }}">
                    <input class="form-control form-control-solid" placeholder="Enter a role name" name="role_name"
                        value="{{ $info->name ?? '' }}" />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Permissions-->
                <div class="fv-row">
                    <!--begin::Label-->
                    <label class="fs-5 fw-bolder form-label mb-2">Role Permissions</label>
                    <!--end::Label-->
                    <!--begin::Table wrapper-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-bold">
                                <!--begin::Table row-->
                                <tr>
                                    <td class="text-gray-800">Administrator Access
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Allows a full access to the system"></i>
                                    </td>
                                    <td>
                                        <!--begin::Checkbox-->
                                        <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
                                            <input class="form-check-input" type="checkbox" value=""
                                                name="" id="kt_roles_select_all" />
                                            <span class="form-check-label" for="kt_roles_select_all">Select all</span>
                                        </label>
                                        <!--end::Checkbox-->
                                    </td>
                                </tr>
                                <!--end::Table row-->
                                <!--begin::Table row-->
                                @php
                                    $perms = [];
                                    if (isset($info->permissions) && !empty($info->permissions)) {
                                        $perms = unserialize($info->permissions);
                                    }
                                @endphp
                                @if (config('constant.permission'))
                                    @foreach (config('constant.permission') as $item)
                                        <tr>
                                            <td class="text-gray-800">
                                                {{ ucwords(str_replace(['-', '_', '.'], ' ', $item)) }}
                                                <input type="hidden" name="item" value="{{ $item ?? '' }}">
                                            </td>
                                            <td>
                                                <div class="d-flex each-row">

                                                    <label
                                                        class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                        <input class="form-check-input common-checkbox enable-row"
                                                            data-id="{{ $item }}" type="checkbox"
                                                            name="{{ $item }}_visible"
                                                            id="{{ $item }}_visible"
                                                            @if (isset($perms[$item][$item . '_visible']) && $perms[$item][$item . '_visible'] == 'on') checked @endif />
                                                        <span class="form-check-label">Read</span>
                                                    </label>

                                                    <label
                                                        class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                        <input class="form-check-input common-checkbox default-disable"
                                                            type="checkbox"
                                                            @if (isset($perms[$item][$item . '_editable']) && $perms[$item][$item . '_editable'] == 'on') checked @endif
                                                            name="{{ $item }}_editable"
                                                            id="{{ $item }}_editable"  @if (isset($perms[$item][$item . '_visible']) && $perms[$item][$item . '_visible'] == 'on') @else disabled @endif />
                                                        <span class="form-check-label">Write</span>
                                                    </label>

                                                    <label
                                                        class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                        <input class="form-check-input common-checkbox default-disable"
                                                            type="checkbox"
                                                            @if (isset($perms[$item][$item . '_delete']) && $perms[$item][$item . '_delete'] == 'on') checked @endif
                                                            name="{{ $item }}_delete"
                                                            id="{{ $item }}_delete" @if (isset($perms[$item][$item . '_visible']) && $perms[$item][$item . '_visible'] == 'on') @else disabled @endif />
                                                        <span class="form-check-label">Delete</span>
                                                    </label>

                                                    <label
                                                        class="form-check form-check-custom form-check-solid me-5 me-lg-20">
                                                        <input class="form-check-input common-checkbox default-disable"
                                                            type="checkbox"
                                                            @if (isset($perms[$item][$item . '_export']) && $perms[$item][$item . '_export'] == 'on') checked @endif
                                                            name="{{ $item }}_export"
                                                            id="{{ $item }}_export" @if (isset($perms[$item][$item . '_visible']) && $perms[$item][$item . '_visible'] == 'on') @else disabled @endif />
                                                        <span class="form-check-label">Export</span>
                                                    </label>

                                                    <label class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input common-checkbox default-disable"
                                                            type="checkbox"
                                                            @if (isset($perms[$item][$item . '_status']) && $perms[$item][$item . '_status'] == 'on') checked @endif
                                                            name="{{ $item }}_status"
                                                            id="{{ $item }}_status" @if (isset($perms[$item][$item . '_visible']) && $perms[$item][$item . '_visible'] == 'on') @else disabled @endif />
                                                        <span class="form-check-label">Status Change</span>
                                                    </label>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!--end::Actions-->

        </div>
        <!--end::Content-->
    </div>
    <!--end::Body-->
    <!--begin::Footer-->
    <div class="card-footer py-5 text-center" id="kt_activities_footer">
        <div class="text-end px-8">
            <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
            <button type="submit" class="btn btn-primary" data-kt-roles-modal-action="submit">
                <span class="indicator-label">Submit</span>
                <span class="indicator-progress">Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>
</form>
<script>
    var add_url = "{{ route('roles.save') }}";

    // Class definition
    var KTUsersAddRole = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_role_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);


        // Init add schedule modal
        var initAddRole = () => {

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'role_name': {
                            validators: {
                                notEmpty: {
                                    message: 'Role name is required'
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
            const submitButton = element.querySelector('[data-kt-roles-modal-action="submit"]');
            submitButton.addEventListener('click', function(e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {
                            const formData = $('#add_role_form').serialize();

                            // Show loading indication
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            // Disable button to avoid multiple click 
                            submitButton.disabled = true;

                            //call ajax call
                            $.ajax({
                                url: add_url,
                                type: "POST",
                                data: formData,
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
                                            text: "Form has been successfully submitted!",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then(function(result) {
                                            if (result.isConfirmed) {
                                                commonDrawer.hide();

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
            const selectAll = form.querySelector('#kt_roles_select_all');
            const allCheckboxes = form.querySelectorAll('[type="checkbox"]');
            var defaultDisable = form.querySelectorAll('.default-disable');
            var enableRow = form.querySelectorAll('.enable-row');

            // Handle check state
            selectAll.addEventListener('change', e => {
                // Apply check state to all checkboxes
                console.log(selectAll.checked);
                if (selectAll.checked) {
                    allCheckboxes.forEach(c => {
                        c.disabled = false;
                        c.checked = e.target.checked;
                    });
                } else {
                    allCheckboxes.forEach(c => {
                        c.disabled = false;
                        c.checked = false;
                    });

                    defaultDisable.forEach(c => {
                        c.disabled = true;
                        c.checked = false;
                    });
                }
            });
            enableRow.forEach(en => {
                
                en.addEventListener('change', e => {
                    
                    let types = $(en).data('id');
                    if (en.checked) {

                        $('#' + types + '_editable').attr('disabled', false);
                        $('#' + types + '_delete').attr('disabled', false);
                        $('#' + types + '_export').attr('disabled', false);
                        $('#' + types + '_status').attr('disabled', false);

                    } else {
                        
                        $('#' + types + '_editable').attr('disabled', true).prop('checked', false);
                        $('#' + types + '_delete').attr('disabled', true).prop('checked', false);
                        $('#' + types + '_export').attr('disabled', true).prop('checked', false);
                        $('#' + types + '_status').attr('disabled', true).prop('checked', false);
                    }
                });
            })


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
        $("#kt_roles_select_all").prop("checked", false);
    });
</script>
