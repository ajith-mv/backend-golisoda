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
<form id="add_charges_form" class="form" action="#" enctype="multipart/form-data">

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
                            <label class="required fw-bold fs-6 mb-2">Title</label>
                            <input type="text" name="title" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Shipping Title" value="{{ $info->shipping_title ?? '' }}" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Pincodes
                                <input type="checkbox" role="button" class="ms-3" name="select_all_pincode" id="select_all_pincode" onchange="changeSelection(this)"> 
                                <span for="select_all_pincode" role="button">Select All</span>
                            </label>
                            <select name="pincodes[]" id="pincode" class="form-control" multiple>
                                @isset($pincodes)
                                    @foreach ($pincodes as $item)
                                        <option value="{{ $item->id }}" @if(in_array($item->id, $usedpincodes)) selected @endif>{{ $item->pincode }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <br>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Minimum Order Amount</label>
                            <input type="text" name="minimum_order_amount" class="form-control form-control-solid mb-3 mb-lg-0 float-number"
                                placeholder="Minimum order amount" value="{{ $info->minimum_order_amount ?? '' }}" />
                        </div>

                        <div class="fv-row mb-7" id="shipping_pane" @if((isset( $info->is_free) && $info->is_free == 'yes' ) ) style="display:none"  @endif>
                            <label class="required fw-bold fs-6 mb-2"> Shipping Charges </label>
                            <input type="text" name="charges" class="form-control form-control-solid mb-3 mb-lg-0 float-number"
                                placeholder="Shipping Charges" value="{{ $info->charges ?? '' }}" />
                        </div>

                        <div class="row mb-7">
                            <div class="col-sm-6">
                                <label class="fw-bold fs-6 mb-2"> Is Free </label>
                                <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_free" id="is_free" value="1" @if((isset( $info->is_free) && $info->is_free == 'yes' ) ) checked @endif />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="fw-bold fs-6 mb-2"> Status </label>
                                <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                    <input class="form-check-input" type="checkbox"  name="status" value="1"  @if((isset( $info->status) && $info->status == 'published' ) || !isset($info->status)) checked @endif />
                                </div>
                            </div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Description</label>
                            <input type="text" name="description" class="form-control form-control-solid mb-3 mb-lg-0"
                                placeholder="Description" value="{{ $info->description ?? '' }}" />
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

    function changeSelection(value){
        if( value.checked ) {
            $("#pincode > option").prop("selected", true);
            $("#pincode").trigger("change");
        } else {
            $("#pincode > option").prop("selected", false);
            $("#pincode").trigger("change");
        }
    }
    
    $('#pincode').select2();
    var add_url = "{{ route('charges.save') }}";
    $('.float-number').keypress(function(event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
    
    $('#is_free').change(()=>{

        if($("#is_free").prop('checked') == true){
            //do something
            $('#shipping_pane').hide();
        } else {
            $('#shipping_pane').show();
        }

    });


    var KTShipping = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_charges_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);
        var initCharges = () => {
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'title': {
                            validators: {
                                notEmpty: {
                                    message: 'Shipping Title is required'
                                }
                            }
                        },
                        'minimum_order_amount': {
                            validators: {
                                notEmpty: {
                                    message: 'Minimum Order Amount is required'
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
                           
                            var form = $('#add_charges_form')[0]; 
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
                initCharges();
            }
        };


    }();
    KTUtil.onDOMContentLoaded(function() {
        KTShipping.init();
    });

</script>