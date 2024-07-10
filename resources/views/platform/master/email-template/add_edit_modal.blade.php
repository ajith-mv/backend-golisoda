
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
<form id="add_email_template_form" class="form" action="#" enctype="multipart/form-data">

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
                            <div class="col-md-8">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Type</label>
                                        <select name="type_id" id="type_id" aria-label="Select a Type" data-control="select2" data-placeholder="Select Type..." class="form-select mb-2">
                                            <option value="">Select Type</option>
                                            @foreach ($subCat as $key=>$val )
                                                <option value="{{ $val->id }}" @if (isset( $info->type_id) && $info->type_id == $val->id) selected @endif>{{ $val->name}}</option>
                                            @endforeach
                                        </select>
                                </div>
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Title</label>
                                    <input type="text" name="title" id="title" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Title" value="{{ $info->title ?? '' }}" />
                                </div>
                                <div class="fv-row mb-7">
                                    <label class="required form-label">Message</label>
                                    <div id="message" name="message" class="min-h-200px mb-2">{!! $info->message ?? '' !!}</div>
                                    <textarea name="message_description" id="message_description" style="display: none;" cols="30" rows="10">{{ $info->message ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-flush py-4">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Params</h2>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="" data-kt-ecommerce-catalog-add-product="auto-options">
                                            
                                            <div id="kt_ecommerce_add_product_options">
                                               
                                                <div class="form-group">
                                                    <div data-repeater-list="kt_ecommerce_add_product_options" class="d-flex flex-column gap-3">
                                                        @if(isset($info['params']))
                                                            @foreach ($info['params'] as $key=>$val )
                                                                <div data-repeater-item="" class="form-group d-flex flex-wrap gap-5">
                                                                    <input type="text" class="form-control mw-100 w-200px" name="kt_ecommerce_add_product_options[{{ $key ?? ''}}][params]" value="{{ $val ?? ''}}" placeholder="Params" />
                                                                    <button type="button" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger">
                                                                        <span class="svg-icon svg-icon-2">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
                                                                                <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                        <div data-repeater-item="" class="form-group d-flex flex-wrap gap-5">
                                                            <input type="text" class="form-control mw-100 w-200px" name="params" value="" placeholder="Params" />
                                                            <button type="button" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger">
                                                                <span class="svg-icon svg-icon-2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                        <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
                                                                        <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
                                                                    </svg>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group mt-5">
                                                    <button type="button" data-repeater-create="" class="btn btn-sm btn-light-primary">
                                                    <span class="svg-icon svg-icon-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <rect opacity="0.5" x="11" y="18" width="12" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor" />
                                                            <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                   Add another params</button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Status </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox"  name="status" value="1"  @if((isset( $info->status) && $info->status == 'published' ) || !isset($info->status)) checked @endif />
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
            <button type="submit" class="btn btn-primary" data-kt-email_template-modal-action="submit">
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
    // $("#message").html("1111");
    var eleme = '#message';
    var quill_meta = document.querySelector('#message');
    quill_meta = new Quill(eleme, {
                modules: {
                    toolbar: [
                        [{
                            header: [1, 2, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        ['image', 'code-block']
                    ]
                },
                placeholder: 'Type your text here...',
                theme: 'snow' // or 'bubble'
            });
            quill_meta.on('text-change', function(delta, oldDelta, source) {
                if(quill_meta.container.firstChild.innerHTML == "<p><br></p>"){
                    $('#message_description').val("");
                }
                else{
                    $('#message_description').val( quill_meta.container.firstChild.innerHTML );
                }
        
    });
</script>
<script>
    $('#type_id').select2();

    var add_url = "{{ route('email-template.save') }}";

    var initFormRepeater = () => {
        $('#kt_ecommerce_add_product_options').repeater({
            
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function () {
                $(this).slideDown();

            },

            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    

    var KTUsersCoupon = function() {
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_email_template_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);
        var initCoupon = () => {
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'type_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Type is required'
                                }
                            }
                        },
                        'title': {
                            validators: {
                                notEmpty: {
                                    message: 'Title is required'
                                }
                            }
                        },
                        'message_description': {
                            validators: {
                                notEmpty: {
                                    message: 'Message is required'
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
            const submitButton = element.querySelector('[data-kt-email_template-modal-action="submit"]');
            submitButton.addEventListener('click', function (e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        
                        if (status == 'Valid') {
                            var form = $('#add_email_template_form')[0]; 
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
                initCoupon();
                initFormRepeater();
                // initConditionsSelect2();
            }
        };
    }();
    KTUtil.onDOMContentLoaded(function() {
        KTUsersCoupon.init();
    });

</script>