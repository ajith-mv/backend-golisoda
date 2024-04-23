@csrf
<input type="hidden" name="id" value="{{ $data->id ?? '' }}">
<input type="hidden" name="tab-name" value="password">
<div class="col-md-8">
    <div class="row fv-row mb-7">
        <div class="col-md-3 text-md-end">
            <label class="fs-6 fw-bold form-label mt-3">
                <span class="required">Old Password</span>
            </label>
        </div>
        <div class="col-md-9">
            <input type="password" class="form-control form-control-solid" name="old_password" value="" />
        </div>
    </div>
    <div class="row fv-row mb-7">
        <div class="col-md-3 text-md-end">
            <label class="fs-6 fw-bold form-label mt-3">
                <span class="required"> Password</span>
            </label>
        </div>
        <div class="col-md-9">
            <input type="password" class="form-control form-control-solid" name="password" value="" />
        </div>
    </div>
    <div class="row fv-row mb-7">
        <div class="col-md-3 text-md-end">
            <label class="fs-6 fw-bold form-label mt-3">
                <span class="required">Change Password</span>
            </label>
        </div>
        <div class="col-md-9">
            <input type="password" class="form-control form-control-solid" name="password_confirmation" value="" />
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-9">

        <div class="card-footer py-5 text-center" id="kt_activities_footer">
            <div class="text-end px-8">
                <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
                <button type="submit" class="btn btn-primary" id="password-submit">
                    <span class="indicator-label">Submit</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>

    </div>
</div>
<script>
    var cancelButton = document.querySelector('#discard');
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
                getTab('password');
            }
        });
    });

    var add_url = "{{ route('my-profile.save') }}";

var KTpassword = function() {

    var form;
    var submitButton;
    var validation;
    var passwordMeter;

    var initAddPassword = () => {
        var validation = FormValidation.formValidation(
            form, {
                fields: {
                    'old_password': {
                        validators: {
                            notEmpty: {
                                message: 'Old Password is required'
                            }
                        }
                    },
                    'password': {
                        validators: {
                            notEmpty: {
                                message: 'New Password is required'
                            },
                            callback: {
                                message: 'Please enter valid password',
                                callback: function(input) {
                                    if (input.value.length > 0) {
                                        return validatePassword();
                                    }
                                }
                            }
                        }
                    },
                    'password_confirmation': {
                        validators: {
                            notEmpty: {
                                message: 'Confirm Password is required'
                            },
                            identical: {
                                compare: function() {
                                    return form.querySelector('[name="password"]').value;
                                },
                                message: 'The password and its confirm are not the same'
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

        const submitButton = document.querySelector('#password-submit');
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();
            validation.revalidateField('password');
            validation.validate().then(function (status) {
                if (status == 'Valid') {
                    var form = $('#kt_ecommerce_general_form')[0]; 
                    var formData = new FormData(form);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });  
                    $.ajax({
                        url: add_url,
                        type:"POST",
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData:false,
                        beforeSend: function() {
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            submitButton.disabled = true;
                        },
                        success: function(res) {
                            if( res.error == 1 ) {
                                submitButton.removeAttribute('data-kt-indicator');
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
                                    text: "Thank you! You've updated Global Settings",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (result) {
                                    if (result.isConfirmed) {
                                        getTab('password')                              
                                    }
                                });
                            }
                        }
                    });
                    
                } else {
                    swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-light-primary"
                        }
                    });
                }
            });
        });
    }

    return {
        init: function () {
            form = document.getElementById('kt_ecommerce_general_form');
            initAddPassword();
        }
    }
}();

KTUtil.onDOMContentLoaded(function() {
    KTpassword.init();
});
</script>
