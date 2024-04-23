"use strict";

// Class definition
var KTUsersAddRole = function () {
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
            form,
            {
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
            }).then(function (result) {
                if (result.value) {
                    commonDrawer.hide();// Hide modal				
                }
            });
        });

        // Submit button handler
        const submitButton = element.querySelector('[data-kt-roles-modal-action="submit"]');
        submitButton.addEventListener('click', function (e) {
            // Prevent default button action
            e.preventDefault();
             // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    if (status == 'Valid') {
                        const formData = $( '#add_role_form').serialize();
                        // Show loading indication
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        // Disable button to avoid multiple click 
                        submitButton.disabled = true;
                       
                        //call ajax call
                        $.ajax({
                            url: add_url,
                            type:"POST",
                            data: formData,
                            beforeSend: function() {
                            },
                            success: function(res) {
                                
                                
                                if( res.error == 1 ) {
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
                                        text: "Form has been successfully submitted!",
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
    const handleSelectAll = () =>{
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
        init: function () {
            initAddRole();
            handleSelectAll();
        }
    };
}();

// On document ready

KTUtil.onDOMContentLoaded(function () {
    KTUsersAddRole.init();
});

$('.common-checkbox').click(function(){
    $("#kt_roles_select_all").prop("checked", false);
});