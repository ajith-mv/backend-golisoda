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
<form id="add_sms_form" class="form" action="#" enctype="multipart/form-data">

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
                                    <label class="required fw-bold fs-6 mb-2">Company Name</label>
                                    <input type="text" name="company_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Company Name" value="{{ $info->company_name ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Template Name</label>
                                    <input type="text" name="template_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Template Name" value="{{ $info->template_name ?? '' }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">PEID Number</label>
                                    <input type="text" name="peid_no" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="PEID number" value="{{ $info->peid_no ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Template DLT Id (refno)</label>
                                    <input type="text" name="tdlt_no" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="TDLD No" value="{{ $info->tdlt_no ?? '' }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Header</label>
                                    <input type="text" name="header" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Header" value="{{ $info->header ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Sms Type</label>
                                    <input type="text" name="sms_type" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Register, Order Processing,.." value="{{ $info->sms_type ?? '' }}" />
                                </div>
                            </div>
                        </div>
                        
                        <div class="fv-row  mb-7">
                            <label class=" required form-label">Template Content Description</label>
                            <textarea name="template_content" id="template_content" class="form-control" cols="30" rows="3">{{ $info->template_content ?? '' }}</textarea>
                        </div>
                        <br>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2"> Published </label>
                            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                <input class="form-check-input" type="checkbox"  name="status" value="1"  @if(isset( $info->status) && $info->status == 'published') checked @elseif(!isset( $info ) && !empty( $info )) checked @endif />
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

       $('#add_sms_form').validate({
           rules: {
                company_name : "required",
                template_name : "required",
                peid_no : "required",
                tdlt_no : "required",
                header : "required",
                sms_type : "required",
                template_content : "required",
           },
           messages: {
                company_name: "Company Name is required",
                template_name: "Template Name is required",
                peid_no: "PEID NO is required",
                tdlt_no: "TDLT NO is required",
                header: "Header is required",
                header: "Sms Type is required",
                template_content: "Template content is required",
           },
           submitHandler: function(form) {
                var action="{{ route('sms-template.save') }}";
                var forms = $('#add_sms_form')[0]; 
                var formData = new FormData(forms);                                       
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });  
                $.ajax({
                    url: "{{ route('sms-template.save') }}",
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
                                text: "Thank you! You've updated Sms Templates",
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
