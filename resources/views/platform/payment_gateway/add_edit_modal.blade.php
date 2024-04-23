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
<style>
    label.error {
        color: red;
    }
</style>
<!--end::Header-->
<!--begin::Body-->
<form id="add_payment_gateway_form" class="form" action="#" enctype="multipart/form-data">

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
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Gateway</label>
                                    <select name="gateway_id" id="gateway_id" class="form-control" >
                                        <option value="">--Select--</option>
                                        @if( isset($gateways->subCategory ) && !empty( $gateways->subCategory )  ) 
                                            @foreach ($gateways->subCategory as $item)
                                                <option value="{{ $item->id }}" @if(isset($info->gateway_id ) && $info->gateway_id == $item->id ) selected @endif>{{ $item->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    
                                </div>
                            </div>
                           
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class=" fw-bold fs-6 mb-2">Access Key</label>
                                    <input type="text" name="access_key" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Access key" value="{{ $info->access_key ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class=" fw-bold fs-6 mb-2">Secret Key</label>
                                    <input type="text" name="secret_key" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Secret key" value="{{ $info->secret_key ?? '' }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2">Merchant Id</label>
                                    <input type="text" name="merchant_id" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Merchant Id" value="{{ $info->merchant_id ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class=" fw-bold fs-6 mb-2">Working Key</label>
                                    <input type="text" name="working_key" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Working key" value="{{ $info->working_key ?? '' }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Live Mode </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox"  name="status" value="1"  @if(isset( $info->mode) && $info->mode == 'live') checked @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Primary </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox"  name="is_primary" value="1"  @if(isset( $info->is_primary) && $info->is_primary == '1') checked @endif />
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
        <div class="text-end ">
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
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>

    $(document).ready(function() {

        // Shared variables
    const element = document.getElementById('kt_common_add_form');
    const drawerEl = document.querySelector("#kt_common_add_form");
    const commonDrawer = KTDrawer.getInstance(drawerEl);

       $('#add_payment_gateway_form').validate({
           rules: {
                gateway_id : "required",
           },
           messages: {
                gateway_id: "Payment Gateway is required",
           },
           submitHandler: function(form) {
                var forms = $('#add_payment_gateway_form')[0]; 
                var formData = new FormData(forms);                                       
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });  
                $.ajax({
                    url: "{{ route('payment-gateway.save') }}",
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
                            // Remove loading indication
                            submitButton.removeAttribute('data-kt-indicator');
                             // Enable button
                            submitButton.disabled = false;
                            let error_msg = res.message
                            Swal.fire({
                                html: res.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                             });
                        } else {
                            
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            dtTable.draw();

                            Swal.fire({
                                // text: "Form has been successfully submitted!",
                                text: "Thank you! You've updated Payment Gateways",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    commonDrawer.hide();
                                }
                            });
                        }
                    }
                });
           }
       });
   });
   
</script>
