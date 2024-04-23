// Close button handler
let closeButton = document.querySelector('[data-kt-roles-modal-action="close"]');
closeButton.addEventListener('click', e => {
    e.preventDefault();

    Swal.fire({
        text: "Are you sure you would like to close?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Yes, close it!",
        cancelButtonText: "No, return",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-active-light"
        }
    }).then(function (result) {
        if (result.value) {
            modal.hide(); // Hide modal				
        } 
    });
});

// Cancel button handler
let cancelButton = document.querySelector('[data-kt-roles-modal-action="cancel"]');
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
            form.reset(); // Reset form	
            modal.hide(); // Hide modal				
        } else if (result.dismiss === 'cancel') {
            Swal.fire({
                text: "Your form has not been cancelled!.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary",
                }
            });
        }
    });
});