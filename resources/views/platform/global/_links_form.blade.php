<form id="linkForm">
    @csrf

    <div class="row">
        <div class="col-lg-12">
            
                @if( isset( $data->links ) && !empty( $data->links ) ) 
                    @foreach ($data->links as $item)
                    <div id="row" class="row p-7">
                        <div class="col-sm-3">
                            <input type="text" name="link_name[]" class="form-control" value="{{ $item->link_name ?? '' }}" placeholder="Link Name" required>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="link_icon[]" class="form-control" value="{{ $item->link_icon ?? '' }}"  placeholder="Link Icon">
                        </div>
                        <div class="col-sm-5">
                            <input type="text" name="link_url[]" class="form-control" value="{{ $item->link_url ?? '' }}"  placeholder="Link Url" required>
                        </div>
                        <div class="col-sm-2">
                            <div class="input-group mt-1">
                                <div class="input-group-prepend">
                                    <button class="btn btn-danger btn-sm" id="DeleteRow" type="button">
                                        <i class="bi bi-trash"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach  
                @else 
                <div id="row" class="row p-7">
                    <div class="col-sm-3">
                        <input type="text" name="link_name[]" class="form-control" placeholder="Link Name" required>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" name="link_icon[]" class="form-control" placeholder="Link Icon">
                    </div>
                    <div class="col-sm-5">
                        <input type="text" name="link_url[]" class="form-control" placeholder="Link Url" required>
                    </div>
                    <div class="col-sm-2">
                        <div class="input-group mt-1">
                            <div class="input-group-prepend">
                                <button class="btn btn-danger btn-sm" id="DeleteRow" type="button">
                                    <i class="bi bi-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>   
                </div>              
                @endif
            </div>

            <div id="newinput"></div>
            <div class="row">
                <div class="col-sm-12  text-end p-11">
                    <button id="rowAdder" type="button" class="btn btn-info">
                        <span class="bi bi-plus-square-dotted">
                        </span> ADD New Row
                    </button>

                    <button type="button" class="btn btn-primary" id="formLinkSubmit" data-kt-order_status-modal-action="submit">
                        <span class="indicator-label">Submit</span>
                        <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
            
        </div>
    </div>
</form>

<script>

    $('#formLinkSubmit').click();
    $("body").on("click", "#formLinkSubmit", function() {

        var forms = $('#linkForm')[0]; 
        var formData = new FormData(forms);     
        event.preventDefault();
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });  
        $.ajax({
            url: "{{ route('global.link.save') }}",
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

                    Swal.fire({
                        // text: "Form has been successfully submitted!",
                        text: "Thank you! You've updated Site Links",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                        }
                    });
                }
            }
        });
        return false;
    });

   

   

   
</script>

