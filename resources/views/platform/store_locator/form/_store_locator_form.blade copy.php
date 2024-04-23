<div class="row mb-7">
    <div class="col-md-10">
        <div class="fv-row mb-7">
            <div class="row">
                <div class="col-md-6">
                    <label class="required fw-bold fs-6 mb-2">Brand</label>
                    <select name="brand_id" id="brand_id" aria-label="Select a Brand" data-control="select2" data-placeholder="Select a Brand..." class="form-select mb-2">
                        <option value="">Select a Brand</option>
                        @isset($brand)
                            @foreach ($brand as $item)
                                <option value="{{ $item->id }}" @if( isset( $info->brand_id ) && $info->brand_id == $item->id ) selected @endif>{{ $item->brand_name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="required fw-bold fs-6 mb-2">Title</label>
                    <input type="text" name="title" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Title" value="{{ $info->title ?? '' }}" />
                </div>
            </div>
        </div>
        <div class="fv-row mb-7">
            <div class="row">
                <div class="col-md-6">
                    <label class="required fw-bold fs-6 mb-2">Description</label>
                    <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Description" name="description" id="description" cols="30" rows="3">{{ $info->description ?? '' }}</textarea>
       
                </div>
                <div class="col-md-6">
                    <label class="fw-bold fs-6 mb-2">Address</label>
                    <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Address" name="address" id="address" cols="30" rows="3">{{ $info->address ?? '' }}</textarea>
                </div>
            </div>
             </div>
       
        <div class="fv-row mb-7">
            <div class="row">
                <div class="col-md-6">
                    <label class="fw-bold fs-6 mb-2">Sorting Order</label>
                    <input type="text" name="order_by" class="form-control numberonly form-control-solid mb-3 mb-lg-0"
                    placeholder="Sorting Order" value="{{ $info->order_by ?? '' }}" min="1" />
                </div>
                <div class="col-md-6">
                    <label class="fw-bold fs-6 mb-2"> Status </label>
                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                        <input class="form-check-input" type="checkbox"  name="status" value="1"  @if( (isset( $info->status) && $info->status == 'published') || (!isset($info->status)))  checked @endif />
                    </div>
                </div>
            </div>
        </div>
        <div class="fv-row mb-5">
            <div class="row">
                <div class="col-md-4">
                    <button id="rowPincode" type="button" class="btn btn-info">
                        <span class="bi bi-plus-square-dotted">
                        </span> ADD Pincode
                    </button>
                    @if( isset( $info->nearPincode ) && !empty( $info->nearPincode ) ) 
                        @foreach ($info->nearPincode as $item)
                        <div id="row" class="row p-4">
                            
                            <div class="col-md-8">
                                <input type="text" name="near_pincode[]" class="form-control" value="{{ $item->pincode ?? '' }}"  placeholder="Pincode">
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group mt-1">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-danger btn-sm" id="DeleteRowPincode" type="button">
                                            <i class="bi bi-trash"></i>
                                            
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach 
                    @endif
                    <div id="newinputPincode"></div>
                </div>
                <div class="col-md-4">
                    <button id="rowContact" type="button" class="btn btn-info">
                        <span class="bi bi-plus-square-dotted">
                        </span> ADD Contact
                    </button>
                    @if( isset( $info->contact ) && !empty( $info->contact ) ) 
                        @foreach ($info->contact as $item)
                        <div id="row" class="row p-4">
                            
                            <div class="col-md-8">
                                <input type="text" name="contact[]" class="form-control numberonly" value="{{ $item->contact ?? '' }}"  placeholder="Contact">
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group mt-1">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-danger btn-sm" id="DeleteRowContact" type="button">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach 
                    @endif
                    <div id="newinputContact"></div>
                </div>
                <div class="col-md-4">
                    <button id="rowEmail" type="button" class="btn btn-info">
                        <span class="bi bi-plus-square-dotted">
                        </span> ADD Email
                    </button>
                    @if( isset( $info->serviceEmail ) && !empty( $info->serviceEmail ) ) 
                        @foreach ($info->serviceEmail as $item)
                        <div id="row" class="row p-4">
                            
                            <div class="col-md-8">
                                <input type="text" name="email[]" class="form-control" value="{{ $item->email ?? '' }}"  placeholder="Email" >
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group mt-1">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-danger btn-sm" id="DeleteRowEmail" type="button">
                                            <i class="bi bi-trash"></i>
                                            
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach 
                    @endif
                    <div id="newinputEmail"></div>
                </div>
            </div>
        </div>
        
    </div>
    <div class="col-md-2">
        <div class=" mb-7">
            <div class="fv-row">
                <label class="d-block fw-bold fs-6 mb-5">Banner Image</label>
                <div class="form-text">
                    Allowed file types: png, jpg,
                    jpeg.
                </div>
            </div>
            <input id="banner_remove_image" type="hidden" name="banner_remove_image" value="no">
            <div class=" image-input image-input-outline banner-image" data-kt-image-input="true"
                style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                @if ($info->banner ?? '')
                @php
                    $url = Storage::url($info->banner);
                @endphp
                    <div class="image-input-wrapper w-125px h-125px banner-image"
                        id="banner-image"
                        style="background-image: url({{ asset($url) }});">
                    </div>
                @else
                    <div class="image-input-wrapper w-125px h-125px banner-image"
                        id="banner-image"
                        style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                    </div>
                @endif
                <label
                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                    title="Change Icon">
                    <i class="bi bi-pencil-fill fs-7"></i>
                    <input type="file" name="banner" id="bannerUrl"
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
                    <i class="bi bi-x fs-2" id="banner_remove_logo"></i>
                </span>
            </div>
        </div>

        <div class=" mb-7">
            <div class="fv-row">
                <label class="d-block fw-bold fs-6 mb-5">Store Image</label>
                <div class="form-text">
                    Allowed file types: png, jpg,
                    jpeg.
                </div>
            </div>
            <input id="store_remove_image" type="hidden" name="store_remove_image" value="no">
            <div class=" image-input image-input-outline store-image" data-kt-image-input="true"
                style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                @if ($info->store_image ?? '')
                @php
                    $url = Storage::url($info->store_image);
                @endphp
                    <div class="image-input-wrapper w-125px h-125px store-image"
                        id="store-image"
                        style="background-image: url({{ asset($url) }});">
                    </div>
                @else
                    <div class="image-input-wrapper w-125px h-125px store-image"
                        id="store-image"
                        style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                    </div>
                @endif
                <label
                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                    title="Change Icon">
                    <i class="bi bi-pencil-fill fs-7"></i>
                    <input type="file" name="store_image" id="storeUrl"
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
                    <i class="bi bi-x fs-2" id="store_remove_logo"></i>
                </span>
            </div>
        </div>
        
    </div>
</div>
<script>
    $('.numberonly').keypress(function (e) {    
        var charCode = (e.which) ? e.which : event.keyCode    
        if (String.fromCharCode(charCode).match(/[^0-9]/g))    
            return false;                        
    }); 
     $("#rowPincode").on("click", function() {
            newRowAdd =
                '<div id="row" class="row p-4">'+
                '<div class="col-md-8">'+
                    '<input type="text" name="near_pincode[]" class="form-control" placeholder="Pincode" required>'+
                '</div>'+
                '<div class="col-md-4">'+
                    '<div class="input-group mt-1">'+
                        '<div class="input-group-prepend">'+
                            '<button class="btn btn-danger btn-sm" id="DeleteRowPincode" type="button">'+
                                '<i class="bi bi-trash"></i>'+
                                
                            '</button>'+
                        '</div>'+                        
                    '</div>'+
                '</div>'+
            '</div>';

            $('#newinputPincode').append(newRowAdd);
        })
        $("body").on("click", "#DeleteRowPincode", function() {
            $(this).parents("#row").remove();
        })

        $("#rowContact").on("click", function() {
            newRowAdd =
                '<div id="row" class="row p-4">'+
                '<div class="col-md-8">'+
                    '<input type="number" name="contact[]" class="form-control numberonly" placeholder="Contact" required>'+
                '</div>'+
                '<div class="col-md-4">'+
                    '<div class="input-group mt-1">'+
                        '<div class="input-group-prepend">'+
                            '<button class="btn btn-danger btn-sm" id="DeleteRowContact" type="button">'+
                                '<i class="bi bi-trash"></i>'+
                               
                            '</button>'+
                        '</div>'+                        
                    '</div>'+
                '</div>'+
            '</div>';

            $('#newinputContact').append(newRowAdd);
        })
        $("body").on("click", "#DeleteRowContact", function() {
            $(this).parents("#row").remove();
        })
 

        $("#rowEmail").on("click", function() {
            newRowAdd =
                '<div id="row" class="row p-4">'+
                '<div class="col-md-8">'+
                    '<input type="text" name="email[]" class="form-control" placeholder="Email" required>'+
                '</div>'+
                '<div class="col-md-4">'+
                    '<div class="input-group mt-1">'+
                        '<div class="input-group-prepend">'+
                            '<button class="btn btn-danger btn-sm" id="DeleteRowEmail" type="button">'+
                                '<i class="bi bi-trash"></i>'+
                               
                            '</button>'+
                        '</div>'+                        
                    '</div>'+
                '</div>'+
            '</div>';

            $('#newinputEmail').append(newRowAdd);
        })
        $("body").on("click", "#DeleteRowEmail", function() {
            $(this).parents("#row").remove();
        })
</script>