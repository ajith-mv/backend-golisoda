<div class="modal fade" id="kt_modal_export" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder" id="export_modal_title"></h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary export_modal_close" id="">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_export_form" class="form" >
                    @csrf
                    <input type="hidden" name="export_type" id="export_type">
                    <div class="fv-row mb-10">
                        <label class="required fs-6 fw-bold form-label mb-2">Select Export Format:</label>
                        <select name="export_format" id="export_format" required data-control="select2" data-placeholder="Select a format" data-hide-search="true" class="form-select form-select-solid fw-bolder">
                            <option value=""> </option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="button" class="btn btn-light me-3 export_modal_close" >Discard</button>
                        <button type="submit" class="btn btn-primary" data-kt-exports-modal-action="submit">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // console.log(config.routes);
    const elementExport = document.getElementById('kt_modal_export_form');
    const submitButton = elementExport.querySelector('[data-kt-exports-modal-action="submit"]');
    submitButton.addEventListener('click', function (e) {
        // Prevent default button action
        e.preventDefault();
        // Validate form before submit
        const formData = $( '#kt_modal_export_form').serialize();
        var export_format = $('#export_format').val();
        var export_type = $('#export_type').val();
        // Show loading indication
        submitButton.setAttribute('data-kt-indicator', 'on');
        // Disable button to avoid multiple click 
        submitButton.disabled = true;
        if( export_format == '' || export_format == undefined ) {
            Swal.fire({
                text: "Sorry, looks like there are some errors detected, please select all fields.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
        } else {
            //call ajax call
            let export_url = config.routes[export_type].export[export_format];
            window.location.href = export_url;
            
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
            exportModal.hide();
        }
   
        
    });
</script>