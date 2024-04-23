<div class="row mb-7">
    <div class="col-md-10">
        <div class="fv-row mb-5">
            <div class="row">
                <div class="col-md-6">
                    <label class="required fw-bold fs-7 mb-2">Brand</label>
                    <select name="brand_id[]" id="brand_id" class="form-control" multiple>
                        <option value="">--select--</option>
                        @isset($brands)
                            @foreach ($brands as $item)
                                <option value="{{ $item->id }}" @if(in_array($item->id, $usedBrands)) selected @endif>{{ $item->brand_name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

            </div>
            <div class="row mt-7">
                <div class="col-md-6 ">
                    <label class="required fw-bold fs-7 mb-2">Title</label>
                    <input type="text" name="title" class="form-control form-control-solid mb-3 mb-lg-0"
                        placeholder="Title" value="{{ $info->title ?? '' }}" />
                </div>
                <div class="col-md-6">
                    <label class="required fw-bold fs-7 mb-2"> Pincode </label>
                    <input class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Pincode" type="text"
                        name="pincode" value="{{ $info->pincode ?? '' }}" />
                </div>

            </div>
            <div class="row mt-7">
                <div class="col-md-12">
                    <label class="required fw-bold fs-7 mb-2">Slug</label>
                    <input type="text" name="slug" class="form-control form-control-solid mb-3 mb-lg-0"
                        placeholder="Slug" value="{{ $info->slug ?? '' }}" />
                </div>
            </div>

        </div>
        <div class="fv-row mb-5">
            <div class="row">
                <div class="col-md-6">
                    <label class="fw-bold fs-7 mb-2"> Is Parent </label>
                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-7 mb-2">
                        <input class="form-check-input" type="checkbox" name="is_parent" id="is_parent" value="1"
                            @if ((isset($info->parent_id) && $info->parent_id == 0) || !isset($info->parent_id)) checked @endif />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fv-row @if ((isset($info->parent_id) && $info->parent_id == 0) || !isset($info->parent_id)) d-none @endif" id="parent-tab">
                        <label class="required fw-bold fs-7 mb-2">Parent Location</label>
                        <select name="parent_location" id="parent_location" aria-label="Select a Location"
                            data-control="select2" data-placeholder="Select a Location..." class="form-select mb-2">

                            @isset($serviceCenter)
                                @foreach ($serviceCenter as $item)
                                    <option value="{{ $item->id }}" @if (isset($info->parent_id) && $info->parent_id == $item->id) selected @endif>
                                        {{ $item->title }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="fv-row mb-5">
            <div class="row">
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-7 mb-2">Whatsapp No</label>
                       <input type="number" class="form-control form-control-solid mb-3 mb-lg-0" maxlength="10" name="whatsapp_no"
                       id="whatsapp_no" value="{{ $info->whatsapp_no ?? ''}}">
                    </div>
                </div>
            </div>
        </div>

        <div class="fv-row mb-5">
            <div class="row">
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-7 mb-2">Description</label>
                        <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Description" name="description"
                            id="description" cols="30" rows="2">{{ $info->description ?? '' }}</textarea>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-7 mb-2">Address</label>
                        <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Address" name="address" id="address"
                            cols="30" rows="2">{{ $info->address ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="fv-row mb-5">
            <div class="row">
                <div class="col-sm-12">
                    <div class="p-3 border border-1">
                        <div class="row">
                            <div class="col-sm-8">
                                <label>Add Pincodes</label>
                            </div>
                            <div class="col-sm-4 text-end">
                                <button id="add_new_pincode" type="button" class="btn btn-info btn-sm">
                                    <span class="bi bi-plus-square-dotted">
                                    </span> Add New
                                </button>
                            </div>
                        </div>
                        <div class="row mt-3" id="pincode_panes">
                            @if (isset($info->nearPincodes))
                                @foreach ($info->nearPincodes as $item)
                                    <div class="col-sm-4">
                                        <input type="text" name="near_pincode[]" class="numberonly form-control mt-3"
                                            placeholder="Pincode" value="{{ $item->pincode }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="col-sm-4">
                                    <input type="text" name="near_pincode[]" class="numberonly form-control mt-3"
                                        placeholder="Pincode">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="near_pincode[]" class="numberonly form-control mt-3"
                                        placeholder="Pincode">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="near_pincode[]" class="numberonly form-control mt-3"
                                        placeholder="Pincode">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="p-3 border border-1">
                        <div class="row">
                            <div class="col-sm-8">
                                <label>Add Contacts</label>
                            </div>
                            <div class="col-sm-4 text-end">
                                <button id="add_new_contact" type="button" class="btn btn-info btn-sm">
                                    <span class="bi bi-plus-square-dotted">
                                    </span> Add New
                                </button>
                            </div>
                        </div>
                        <div class="row mt-3" id="contant_panes">
                            @if (isset($info->contacts))
                                @foreach ($info->contacts as $item)
                                    <div class="col-sm-4">
                                        <input type="text" name="contact[]" maxlength="10" class="numberonly form-control mt-3"
                                            placeholder="Contact No" value="{{ $item->contact }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="col-sm-4">
                                    <input type="text" name="contact[]" maxlength="10" class="numberonly form-control mt-3"
                                        placeholder="Contact No">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="contact[]" maxlength="10"  class="numberonly form-control mt-3"
                                        placeholder="Contact No">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="contact[]" maxlength="10" class="numberonly form-control mt-3"
                                        placeholder="Contact No">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="p-3 border border-1">
                        <div class="row">
                            <div class="col-sm-8">
                                <label>Add Email</label>
                            </div>
                            <div class="col-sm-4 text-end">
                                <button id="add_new_email" type="button" class="btn btn-info btn-sm">
                                    <span class="bi bi-plus-square-dotted">
                                    </span> Add New
                                </button>
                            </div>
                        </div>
                        <div class="row mt-3" id="email_pane">
                            @if (isset($info->emails))
                                @foreach ($info->emails as $item)
                                    <div class="col-sm-4">
                                        <input type="text" name="email[]" class="form-control mt-3"
                                            placeholder="Email" value="{{$item->email}}">
                                    </div>
                                @endforeach
                            @else
                                <div class="col-sm-4">
                                    <input type="text" name="email[]" class="form-control mt-3" placeholder="Email">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="email[]" class="form-control mt-3" placeholder="Email">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="email[]" class="form-control mt-3" placeholder="Email">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="fv-row mb-7 mt-3">
                        <label class="fw-bold fs-7 mb-2">Map Link</label>
                        <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Map Link" name="map_link"
                            id="map_link" cols="30" rows="2">{{ $info->map_link ?? '' }}</textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-7 mb-2">360 Deg image Link</label>
                        <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="360 Image Link" name="image_360_link"
                            id="image_360_link" cols="30" rows="2">{{ $info->image_360_link ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class=" mb-7">
            <div class="fv-row">
                <label class="d-block fw-bold fs-7 mb-5">Banner Image</label>
                <div class="form-text small">
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
                    <div class="image-input-wrapper w-125px h-125px banner-image" id="banner-image"
                        style="background-image: url({{ asset($url) }});">
                    </div>
                @else
                    <div class="image-input-wrapper w-125px h-125px banner-image" id="banner-image"
                        style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                    </div>
                @endif
                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Icon">
                    <i class="bi bi-pencil-fill fs-7"></i>
                    <input type="file" name="banner" id="bannerUrl" accept=".png, .jpg, .jpeg" />
                </label>

                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                    <i class="bi bi-x fs-2"></i>
                </span>
                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar1">
                    <i class="bi bi-x fs-2" id="banner_remove_logo"></i>
                </span>
            </div>
        </div>
        <div class=" mb-7">
            <div class="fv-row">
                <label class="d-block fw-bold fs-7 mb-5">Service Center Image</label>
                <div class="form-text">
                    Allowed file types: png, jpg,
                    jpeg.
                </div>
            </div>
            <input id="banner_mobile_remove_image" type="hidden" name="banner_mobile_remove_image" value="no">
            <div class="image-input image-input-outline banner-mobile-image" data-kt-image-input="true"
                style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                @if ($info->banner_mb ?? '')
                    @php
                        $url = Storage::url($info->banner_mb);
                        // print_r( $url );
                    @endphp
                    <div class="image-input-wrapper w-125px h-125px banner-mobile-image" id="banner-mobile-image"
                        style="background-image: url({{ asset($url) }});">
                    </div>
                @else
                    <div class="image-input-wrapper w-125px h-125px banner-mobile-image" id="banner-mobile-image"
                        style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                    </div>
                @endif
                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change Icon">
                    <i class="bi bi-pencil-fill fs-7"></i>
                    <input type="file" name="banner_mb" id="bannerMobileUrl" accept=".png, .jpg, .jpeg" />
                </label>

                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                    <i class="bi bi-x fs-2"></i>
                </span>
                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar1">
                    <i class="bi bi-x fs-2" id="banner_mobile_remove_logo"></i>
                </span>
            </div>
        </div>

        <div class="mb-7 mt-4">
            <label class="fw-bold fs-7 mb-2">Sorting Order</label>
            <input type="text" name="order_by" class="form-control numberonly form-control-solid mb-3 mb-lg-0"
                placeholder="Sorting Order" value="{{ $info->order_by ?? '' }}" min="1" />
        </div>
        <div class="mb-7 mt-4">
            <label class="fw-bold fs-7 mb-2"> Status </label>
            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-7 mb-2">
                <input class="form-check-input" type="checkbox" name="status" value="1"
                    @if ((isset($info->status) && $info->status == 'published') || !isset($info->status)) checked @endif />
            </div>
        </div>
    </div>
</div>
<script>

    $('#brand_id').select2();
    $('.numberonly').keypress(function(e) {
        var charCode = (e.which) ? e.which : event.keyCode
        if (String.fromCharCode(charCode).match(/[^0-9]/g))
            return false;
    });

    var add_new_email = document.getElementById('add_new_email');
    var add_new_contact = document.getElementById('add_new_contact');
    var add_new_pincode = document.getElementById('add_new_pincode');

    add_new_email.addEventListener('click', function() {
        let email_html = `<div class="col-sm-4">
                                <input type="text" name="email[]" class="form-control mt-3" placeholder="Email">
                            </div>`;
        $('#email_pane').append(email_html);
    })

    add_new_contact.addEventListener('click', function() {
        let contact_html = `<div class="col-sm-4">
                                <input type="text" name="contact[]" class="numberonly form-control mt-3" placeholder="Contact No">
                            </div>`;
        $('#contant_panes').append(contact_html);
    })

    add_new_pincode.addEventListener('click', function() {
        let pincode_html = `<div class="col-sm-4">
                                <input type="text" name="near_pincode[]" maxlength="10" class="numberonly form-control mt-3" placeholder="Pincode">
                            </div>`;
        $('#pincode_panes').append(pincode_html);
    })
</script>
