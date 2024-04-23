
<div class="d-flex flex-column gap-7 gap-lg-10">
    <div class="card card-flush py-4">
        <div class="card-body pt-2">           

            <div class="col-md-4">
                <button type="button" id="btnAddUrl" class="btn btn-sm btn-light-primary ">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr087.svg-->
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <rect opacity="0.5" x="11" y="18" width="12" height="2"
                                rx="1" transform="rotate(-90 11 18)" fill="currentColor" />
                            <rect x="6" y="11" width="12" height="2" rx="1"
                                fill="currentColor" />
                        </svg>
                    </span>

                    <!--end::Svg Icon-->Add URL
                </button><br>

            </div>
           

            
            <div id="newinput_url" class="mt-5">
                @isset($info->productUrl)
                @foreach ($info->productUrl as $itemUrl)
                <div class="card border border-2 p-5" > 
                    <div class="row mt-5">
            
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-10 fv-row">
                                <label class="form-label">Thumbnail URL</label>
                                <input type="text" name="thumbnail_url[]" class="form-control mb-2" placeholder="Thumbnail URL" value="{{ $itemUrl->thumbnail_url ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-10 fv-row">
                                <label class="form-label">Video URL</label>
                                <input type="text" name="video_url[]" class="form-control mb-2" placeholder="Video URL" value="{{ $itemUrl->video_url ?? '' }}" />
                            </div>
                        </div>
                    </div>  
                    
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-10 fv-row">
                                <label class="form-label">Description </label>
                               <textarea name="url_desc[]" id="url_desc" class="form-control">{{ $itemUrl->description ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-10 fv-row">
                                <label class="form-label">Sorting Order</label>
                                <input type="text" name="url_order_by[]" id="url_order_by" class="form-control mb-2" placeholder="Sorting Order" value="{{ $itemUrl->order_by ?? '' }}" />
                            </div>
                        </div>
                            <div class="col-md-1">
                                <button type="button"  
                                    class="btn btn-sm btn-icon btn-light-danger mt-10" onclick="return romove_url_row({{$itemUrl->id}})">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="7.05025" y="15.5356" width="12"
                                                height="2" rx="1"
                                                transform="rotate(-45 7.05025 15.5356)" fill="currentColor">
                                            </rect>
                                            <rect x="8.46447" y="7.05029" width="12" height="2"
                                                rx="1" transform="rotate(45 8.46447 7.05029)"
                                                fill="currentColor"></rect>
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </button>
                            </div> 
                    </div> 
                     
                
            </div> 
                </div>
                @endforeach
                @endisset

             
            </div>
        </div>
    </div>  
</div>  


<style>
  /*  .select2-container .select2-selection {
    height: 50px;
  
    overflow: scroll;
} */
    </style>
    <script>
          $("#btnAddUrl").click(function() {

newRowAdd_url =
    ` <div class="card border border-2 p-5 new_row_add_url" id='new_row_add_url'> 
        <div class="row mt-5">
                <div class="col-md-5">
                    <div class="mb-10 fv-row">
                        <label class="form-label">Thumbnail URL</label>
                        <input type="text" name="thumbnail_url[]" class="form-control mb-2" placeholder="Thumbnail URL" value="" />
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-10 fv-row">
                        <label class="form-label">Video URL</label>
                        <input type="text" name="video_url[]" class="form-control mb-2" placeholder="Video URL" value="" />
                    </div>
                </div>
            </div>  
            
            <div class="row">
                <div class="col-md-5">
                    <div class="mb-10 fv-row">
                        <label class="form-label">Description </label>
                       <textarea name="url_desc[]" id="url_desc" class="form-control"></textarea>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-10 fv-row">
                        <label class="form-label">Sorting Order</label>
                        <input type="text" name="url_order_by[]" id="url_order_by" class="form-control mb-2" placeholder="Sorting Order" value="" />
                    </div>
                </div>
          
            
            <div class="col-md-1">
                <button type="button"  
                    class="btn btn-sm btn-icon btn-light-danger removeDescRow mt-10">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="7.05025" y="15.5356" width="12"
                                height="2" rx="1"
                                transform="rotate(-45 7.05025 15.5356)" fill="currentColor">
                            </rect>
                            <rect x="8.46447" y="7.05029" width="12" height="2"
                                rx="1" transform="rotate(45 8.46447 7.05029)"
                                fill="currentColor"></rect>
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </button>
            </div>
        </div> 
        </div>
    </div>`;

$('#newinput_url').append(newRowAdd_url);
});

// function removeDescritionRow(event) {
//     alert();
//     console.log(this);
//     console.log($(this).parent('#new_row_add'));
//     // $(this).parent('#new_row_add').remove();

// }

$(document).on("click", ".removeDescRow", function() {
$(this).parents('#new_row_add_url').remove();
});

function romove_url_row(id)
{
    Swal.fire({
                text: "Are you sure you would like to delete this record?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, Delete it!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function(result) {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{ route('products_url.delete') }}",
                        type: 'POST',
                        data: {
                            id: id,
                        },
                        success: function(res) {
                            location.reload();
                            Swal.fire({
                                title: "Updated!",
                                text: res.message,
                                icon: "success",
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-success"
                                },
                                timer: 3000
                            });

                        },
                        error: function(xhr, err) {
                            if (xhr.status == 403) {
                                toastr.error(xhr.statusText, 'UnAuthorized Access');
                            }
                        }
                    });
                }
            });
}
        </script>