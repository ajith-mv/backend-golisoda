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
<form id="add_user_form" class="form" action="#" enctype="multipart/form-data">

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
                            <label class="required fw-bold fs-6 mb-2">Full Name </label>
                            <input type="text" name="user_name" value="{{ $info->name ?? '' }}"
                                class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Full name" />
                        </div>
                        <input type="hidden" id="user_id" name="id" value="{{ $info->id ?? '' }}">

                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Email</label>
                            <input type="email" name="user_email" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="example@domain.com" value="{{ $info->email ?? '' }}" />
                        </div>
                        {{-- @if (!isset($info->id)) --}}
                            <div class="fv-row mb-7">
                                <label class="{{ (!isset($info->id)) ? 'required' : ''}} fw-bold fs-6 mb-2">Password</label>
                                <input type="password" name="password"
                                    class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Password" />
                            </div>
                        {{-- @endif --}}
                        <div class="fv-row mb-7">

                            <label class="required fw-bold fs-6 mb-2">Mobile</label>

                            <div class="row">
                                <div class="col-md-2">
                                    <select name="country_code" class="form-control">
                                        @foreach($country_code as $key=>$val)
                                        <option value="+{{ $val['phone_code'] }}" @if (isset($info->country_code) && $info->country_code == $val->phone_code) selected @endif>+{{ $val['phone_code'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" min="0" maxlength="10" max="10" name="mobile_no"
                                        class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                        value="{{ $info->mobile_no ?? '' }}" placeholder="Mobile" />
                                </div>

                            </div>

                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Address</label>

                            <textarea name="address" id="address" class="form-control form-control-solid mb-3 mb-lg-0" cols="30"
                                rows="3">{{ $info->address ?? '' }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="">
                                    <!--begin::Label-->
                                    <label class="required fw-bold fs-6 mb-5">Role</label>
                                    @foreach ($role as $item)
                                        <div class="d-flex fv-row">
                                            <div class="form-check form-check-custom form-check-solid w-100">

                                                <input class="form-check-input me-3" value="{{ $item->id }}"
                                                    name="user_role" type="radio"
                                                    @if (isset($info->role_id) && $info->role_id == $item->id) checked @endif
                                                    id="kt_modal_update_role_option_{{ $item->id }}" />
                                                <label class="form-check-label"
                                                    for="kt_modal_update_role_option_{{ $item->id }}">
                                                    <div class="fw-bolder text-gray-800"> {{ $item->name }}</div>
                                                </label>
                                            </div>

                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-4">

                                <div class="fv-row mb-7">
                                    <label class="d-block fw-bold fs-6 mb-5">Avatar</label>

                                    <div class="form-text">Allowed file types: png, jpg,
                                        jpeg.</div>
                                </div>
                                <input id="image_remove" type="hidden" name="image_remove" value="no">
                                <div class="image-input image-input-outline manual-image" data-kt-image-input="true"
                                    style="background-image: url({{ asset('userImage/dummy.jpeg') }})">
                                    @if ($info->image ?? '')
                                        <div class="image-input-wrapper w-125px h-125px manual-image"
                                            id="manual-image"
                                            style="background-image: url({{ asset('/') . $info->image }});">
                                        </div>
                                    @else
                                        <div class="image-input-wrapper w-125px h-125px manual-image"
                                            id="manual-image"
                                            style="background-image: url({{ asset('userImage/dummy.jpeg') }});">
                                        </div>
                                    @endif
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                        title="Change avatar">
                                        <i class="bi bi-pencil-fill fs-7"></i>
                                        <input type="file" name="avatar" id="readUrl"
                                            accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" />
                                        {{-- <input type="file" name="userImage" id="userImage"> --}}
                                    </label>

                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                        title="Cancel avatar">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                        title="Remove avatar1">
                                        <i class="bi bi-x fs-2" id="avatar_remove"></i>
                                    </span>
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
            <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
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

    document.getElementById('readUrl').addEventListener('change', function() {
        console.log("111");
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
    document.getElementById('avatar_remove').addEventListener('click', function() {
        $('#image_remove').val("yes");
        $('#manual-image').css({
            'background-image': ''
        });
    });


    var add_url = "{{ route('users.save') }}";

    // Class definition
    var KTUsersAddRole = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_user_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);


        // Init add schedule modal
        var initAddRole = () => {

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'user_name': {
                            validators: {
                                notEmpty: {
                                    message: 'User name is required'
                                }
                            }
                        },
                        'user_email': {

                            validators: {

                                notEmpty: {

                                    message: 'Email is required'
                                }
                            }
                        },
                        'password': (document.getElementById('user_id').value !== '') ? {} : {
                            validators: {
                                notEmpty: {
                                    message: 'Password is required'
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
                        'user_role': {
                            validators: {
                                notEmpty: {
                                    message: 'User Role is required'
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
            const submitButton = element.querySelector('[data-kt-users-modal-action="submit"]');
            // submitButton.addEventListener('click', function(e) {
            $('#add_user_form').submit(function(e) {
                // alert()
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {

                            var formData = new FormData(document.getElementById(
                                "add_user_form"));
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
            const selectAll = form.querySelector('#kt_roles_select_all');
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
        $("#kt_roles_select_all").prop("checked", false);
    });
</script>
