@csrf
<input type="hidden" name="id" value="{{ $data->id ?? '' }}">
<input type="hidden" name="tab_name" value="myaccount">

<div class="row">
    <div class="col-md-6">
        <div class="row fv-row mb-7 ">
            <div class="col-md-3 text-md-end">
                <label class="required fs-6 fw-bold form-label mt-3">
                    Name
                </label>
            </div>
            <div class="col-md-9">
                <input type="text" class="form-control form-control-solid mb-3 mb-lg-0" name="name" value="{{ $data->name ?? '' }}" />
            </div>
        </div>
        <div class="row fv-row mb-7">
            <div class="col-md-3 text-md-end">
                <label class="fs-6 fw-bold form-label mt-3">
                    <span class="required">Email</span>
                    
                </label>
            </div>
            <div class="col-md-9">
                <input type="text" class="form-control form-control-solid"   name="email" value="{{ $data->email ?? '' }}" readonly />
            </div>
        </div>
        <div class="row fv-row mb-7">
            <div class="col-md-3 text-md-end">
                <label class="fs-6 fw-bold form-label mt-3">
                    <span class="required">Mobile</span>
                </label>
            </div>
            <div class="col-md-9">
                <input type="text" class="form-control form-control-solid numberonly" maxlength="10"  name="mobile_number" value="{{ $data->mobile_no ?? '' }}" />
            </div>
        </div>
        
        <div class="row fv-row mb-7">
            <div class="col-md-3 text-md-end">
                <label class="fs-6 fw-bold form-label mt-3">
                    <span>Address</span>
                    
                </label>
            </div>
            <div class="col-md-9">
                <textarea class="form-control form-control-solid" name="address">{{ $data->address ?? '' }}</textarea>
            </div>
        </div>
    </div>
    <div class="col-md-1">

    </div>
    <div class="col-md-5">
        <div class="col-md-4">

            <div class="fv-row mb-7">
                <label class="d-block fw-bold fs-6 mb-5">Profile Image</label>

                <div class="form-text">Allowed file types: png, jpg,
                    jpeg.</div>
            </div>
            <input id="image_remove_image" type="hidden" name="image_remove_image" value="no">
            <div class="image-input image-input-outline manual-image" 
                style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                @if ($data->image ?? '')
                    <div class="image-input-wrapper w-125px h-125px manual-image"
                        id="manual-image"
                        style="background-image: url({{ asset('/') . $data->image }});">
                    </div>
                @else
                    <div class="image-input-wrapper w-125px h-125px manual-image"
                        id="manual-image"
                        style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                    </div>
                @endif
                <label
                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                    title="Change avatar">
                    <i class="bi bi-pencil-fill fs-7"></i>
                    <input type="file" name="profile_image" id="profile_image" 
                        accept=".png, .jpg, .jpeg" />
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
                    <i class="bi bi-x fs-2" id="avatar_remove_logo"></i>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-9 offset-md-3">
        <div class="separator mb-6"></div>
        
        <div class="card-footer py-5 text-center" id="kt_activities_footer">
            <div class="text-end px-8">
                <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
                <button type="submit" class="btn btn-primary" id="form-submit" data-kt-ecommerce-settings-type="submit">
                    <span class="indicator-label">Submit</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
 $(document).ready(function () {    
    $('.numberonly').keypress(function (e) {    
        var charCode = (e.which) ? e.which : event.keyCode    
        if (String.fromCharCode(charCode).match(/[^0-9]/g))    
            return false;                        
    });    

}); 
document.getElementById('profile_image').addEventListener('change', function() {
 
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

var cancelButton = document.getElementById('discard');
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
            location.reload();
        }
    });
});

var add_url = "{{ route('my-profile.save') }}";

var KTmyAccount = function() {

    var form;
    var submitButton;
    var validation;
   
    var initAddProfile = () => {
        var validation = FormValidation.formValidation(
            form, {
                fields: {
                    'name': {
                        validators: {
                            notEmpty: {
                                message: 'Name is required'
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
                    'mobile_number': {
                        validators: {
                            notEmpty: {
                                message: 'Mobile Number is required'
                            },
                            stringLength: {
                                max: 10,
                                min: 10,
                                message: 'The phone number must be 10 characters',
                            },
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

        const submitButton = document.querySelector('#form-submit');
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();
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
                                        // getTab('myaccount');
                                        location.reload();
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
            initAddProfile();
        }
    }
}();

KTUtil.onDOMContentLoaded(function() {
    KTmyAccount.init();
});
    


</script>