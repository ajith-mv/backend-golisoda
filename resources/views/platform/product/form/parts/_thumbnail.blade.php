<div class="card-header">
    <div class="card-title">
        <h3>Thumbnail</h3>
    </div>
</div>

<div class="card-body text-center pt-0">
    <!--begin::Image input-->
    @if( isset($info->base_image) && !empty( $info->base_image ) )
    @php
        $url = Storage::url($info->base_image);
        // print_r( $url );
    @endphp
    @endif
    <div class="image-input image-input-empty image-input-outline mb-3" data-kt-image-input="true"
        @if( isset($info->base_image) && !empty( $info->base_image ) )  style="background-image: url({{ asset($url) }})"  @else style="background-image: url(../../../assets/media/svg/files/blank-image.svg)" @endif >
        <!--begin::Preview existing avatar-->
        <div class="image-input-wrapper w-150px h-150px"></div>
        <!--end::Preview existing avatar-->
        <!--begin::Label-->
        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
            <i class="bi bi-pencil-fill fs-7"></i>
            <!--begin::Inputs-->
            <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
            <input type="hidden" name="avatar_remove" />
            <!--end::Inputs-->
        </label>
        <!--end::Label-->
        <!--begin::Cancel-->
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
            <i class="bi bi-x fs-2"></i>
        </span>
        <!--end::Cancel-->
        <!--begin::Remove-->
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
            <i class="bi bi-x fs-2"></i>
        </span>
        <!--end::Remove-->
    </div>
    <!--end::Image input-->
    <!--begin::Description-->
    <div class="text-muted small">Set the product thumbnail image. Only *.png, *.jpg and *.jpeg image files are accepted
    </div>
    <!--end::Description-->
</div>
