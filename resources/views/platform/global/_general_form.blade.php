<form id="kt_account_global_form" class="form" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="1">
    <input type="hidden" name="type" value="general">

    <div class="card-body border-top p-9">
      
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Favicon</label>
            <div class="col-lg-8">
                <input id="image_remove_favicon" type="hidden" name="image_remove_favicon" value="no">
                <div class="image-input image-input-outline manual-image-favicon" data-kt-image-input="true"
                    style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                    @if ($data->favicon ?? '')
                        <div class="image-input-wrapper w-125px h-125px manual-image-favicon"
                            id="manual-image-favicon"
                            style="background-image: url({{ asset('/') . $data->favicon }});">
                        </div>
                    @else
                        <div class="image-input-wrapper w-125px h-125px manual-image-favicon"
                            id="manual-image-favicon"
                            style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                        </div>
                    @endif
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                        title="Change avatar">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="favicon" id="readUrlfavicon"
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
                        <i class="bi bi-x fs-2" id="avatar_remove_favicon"></i>
                    </span>
                </div>
            </div>
        </div> 
        <br>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Logo</label>
            <div class="col-lg-8">
                <input id="image_remove_logo" type="hidden" name="image_remove_logo" value="no">
                <div class="image-input image-input-outline manual-image-logo" data-kt-image-input="true"
                    style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                    @if ($data->logo ?? '')
                        <div class="image-input-wrapper w-125px h-125px manual-image-logo"
                            id="manual-image-logo"
                            style="background-image: url({{ asset('/') . $data->logo }});">
                        </div>
                    @else
                        <div class="image-input-wrapper w-125px h-125px manual-image-logo"
                            id="manual-image-logo"
                            style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                        </div>
                    @endif
                    <label
                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                        title="Change avatar">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="logo" id="readUrllogo"
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
                        <i class="bi bi-x fs-2" id="avatar_remove_logo"></i>
                    </span>
                </div>
            </div>
        </div> 
       
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label required fw-bold fs-6">Site Name</label>
            <div class="col-lg-8 fv-row">
                <input type="text" name="site_name" class="form-control form-control-lg form-control-solid" placeholder="Site name" value="{{ $data->site_name ?? '' }}" />
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">
                <span class="required">Site Phone</span>
            </label>
            <div class="col-lg-8 fv-row">
                <input type="text" name="site_mobile_number" class="form-control form-control-lg form-control-solid" placeholder="Site Phone number" value="{{ $data->site_mobile_no ?? '' }}" />
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">
                <span class="required">Site Email</span>
            </label>
            <div class="col-lg-8 fv-row">
                <input type="email" name="site_email" class="form-control form-control-lg form-control-solid" placeholder="Site Email" value="{{ $data->site_email ?? '' }}" />
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Address</label>
            <div class="col-lg-8 fv-row">
                <textarea name="address" class="form-control form-control-lg form-control-solid" id="address" cols="30" rows="3">{{ $data->address ?? '' }}</textarea>
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Copyrights</label>
            <div class="col-lg-8 fv-row">
                <input type="text" name="copyrights" class="form-control form-control-lg form-control-solid" placeholder="Copyrights" value="{{ $data->copyrights ?? '' }}" />
            </div>
        </div>
         <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Show Pick-up From Store</label>
            <div class="col-lg-8 fv-row mt-3">
                <input class="form-check-input" type="checkbox" name="is_pickup_from_store" @if($data->is_pickup_from_store==1) checked @endif/>
            </div>
        </div>
         <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">COD</label>
            <div class="col-lg-8 fv-row mt-3">
                <input class="form-check-input" type="checkbox" name="is_cod" @if($data->is_cod==1) checked @endif/>
            </div>
        </div>
         <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">COD Amount</label>
            <div class="col-lg-8 fv-row">
                  <input type="text" name="cod_amount" class="form-control form-control-lg form-control-solid" placeholder="COD Amount" value="{{ $data->cod_amount ?? '' }}" />
            </div>
        </div>
    </div>
    <!--end::Card body-->
    <!--begin::Actions-->
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="submit" class="btn btn-primary" id="kt_account_global_submit">Save Changes</button>
    </div>
    <!--end::Actions-->
</form>
<script>
    
    document.getElementById('readUrlfavicon').addEventListener('change', function() {
        
        if (this.files[0]) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
                let img_url = event.target.result;
                $('#manual-image-favicon').css({
                    'background-image': 'url(' + event.target.result + ')'
                });
            });
        }
    });
    document.getElementById('avatar_remove_favicon').addEventListener('click', function() {
        
        $('#image_remove_favicon').val("yes");
        $('#manual-image-favicon').css({
            'background-image': ''
        });
    });

    document.getElementById('readUrllogo').addEventListener('change', function() {
        console.log("111");
        if (this.files[0]) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
                // console.log(event.target);
                let img_url = event.target.result;
                $('#manual-image-logo').css({
                    'background-image': 'url(' + event.target.result + ')'
                });
            });
        }
    });
    document.getElementById('avatar_remove_logo').addEventListener('click', function() {
        $('#image_remove_logo').val("yes");
        $('#manual-image-logo').css({
            'background-image': ''
        });
    });
</script>
