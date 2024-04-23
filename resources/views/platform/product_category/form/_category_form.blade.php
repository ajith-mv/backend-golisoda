<div class="row mb-7">
    <div class="col-md-8">
        <div class="fv-row mb-7">
            <label class="required fw-bold fs-6 mb-2">Category Name</label>
            <input type="text" name="name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Category Name" value="{{ $info->name ?? '' }}" />
        </div>
        <div class="fv-row mb-7">
            <label class="fw-bold fs-6 mb-2"> Is Parent </label>
            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                <input class="form-check-input" type="checkbox"  name="is_parent" id="is_parent" value="yes" @if( (isset( $info->parent_id ) && $info->parent_id == 0 ) || !isset($info->parent_id) ) checked @endif />
            </div>
            <div class="fv-row @if( (isset( $info->parent_id ) && $info->parent_id == 0 ) || !isset($info->parent_id) ) d-none @endif" id="parent-tab">
                <label class="required fw-bold fs-6 mb-2">Parent Category</label>
                <select name="parent_category" id="parent_category" aria-label="Select a Language" data-control="select2" data-placeholder="Select a Language..." class="form-select mb-2">

                    @isset($productCategory)
                        @foreach ($productCategory as $item)
                            <option value="{{ $item->id }}" @if( isset( $info->parent_id ) && $info->parent_id == $item->id ) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
        </div>
        <div class="fv-row mb-7">
            <label class="fw-bold fs-6 mb-2">Tag Line</label>
            <input type="text" name="tag_line" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Tag line" value="{{ $info->tag_line ?? '' }}" />
        </div>
        <div class="fv-row mb-7">
            <label class="fw-bold fs-6 mb-2">Description</label>
            <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Description" name="description" id="description" cols="30" rows="5">{{ $info->description ?? '' }}</textarea>
        </div>
        <div class="mb-7 mt-10">
            <label class="fw-bold fs-6 mb-2"> Published </label>
            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                <input class="form-check-input" type="checkbox"  name="status" value="1"  @if( (isset( $info->status) && $info->status == 'published') || (!isset($info->status)))  checked @endif />
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class=" mb-7">
            <div class="fv-row">
                <label class="d-block fw-bold fs-6 mb-5">Image</label>
                <div class="form-text">
                    Allowed file types: png, jpg,
                    jpeg.
                </div>
            </div>
            <input id="image_remove_image" type="hidden" name="image_remove_image" value="no">
            <div class="image-input image-input-outline manual-image" data-kt-image-input="true"
                style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                @if ($info->image ?? '')
                @php
                    $url = Storage::url($info->image);
                    // print_r( $url );
                @endphp
                    <div class="image-input-wrapper w-125px h-125px manual-image"
                        id="manual-image"
                        style="background-image: url({{ asset($url) }});">
                    </div>
                @else
                    <div class="image-input-wrapper w-125px h-125px manual-image"
                        id="manual-image"
                        style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                    </div>
                @endif
                <label
                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                    title="Change Icon">
                    <i class="bi bi-pencil-fill fs-7"></i>
                    <input type="file" name="categoryImage" id="readUrl"
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
        
        <div class="fv-row mb-5">
            <label class="fw-bold fs-6 mb-2"> Is Tax </label>
            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                <input class="form-check-input" type="checkbox"  name="is_tax" id="is_tax" value="on" @if( isset( $info->tax_id ) && !empty( $info->tax_id ) ) checked @endif />
            </div>
            <div class="fv-row @if( isset( $info->tax_id ) && !empty( $info->tax_id ) ) @else d-none @endif" id="tax-tab">
                <label class="required fw-bold fs-6 mb-2">Taxes</label>
                <select name="tax_id" id="tax_id"  class="form-select mb-2">
                    <option value="">--select Tax--</option>
                    @isset($taxAll)
                        @foreach ($taxAll as $item)
                            <option value="{{ $item->id }}" @if( isset( $info->tax_id ) && $info->tax_id == $item->id ) selected @endif>{{ $item->title  }} ({{ $item->pecentage }}%)</option>
                        @endforeach
                    @endisset
                </select>
            </div>
        </div>

        <div class="fv-row mb-5">
            <label class="fw-bold fs-6 mb-2"> Is Show on Menu </label>
            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                <input class="form-check-input" type="checkbox"  name="is_home_menu" id="is_home_menu" value="on" @if( isset( $info->is_home_menu ) && $info->is_home_menu == 'yes' ) checked @endif />
            </div>
        </div>
        <div class="mb-7 mt-4">
            <label class="required fw-bold fs-6 mb-2">Sorting Order</label>
            <input type="text" name="order_by" id="order_by" class="form-control numberonly form-control-solid mb-3 mb-lg-0"
                placeholder="Sorting Order" value="{{ $info->order_by ?? '' }}" min="1" />
        </div>
         <div class="mb-7 mt-4">
            <label class="fw-bold fs-6 mb-2"> Is Show on Home Page </label>
            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                <input class="form-check-input" type="checkbox"  name="is_home_page" id="is_home_page" value="on" @if( isset( $info->is_home_page ) && $info->is_home_page == 1 ) checked @endif />
            </div>
        </div>
         <div class="mb-7 mt-4">
              <div class="fv-row">
                <label class="d-block fw-bold fs-6 mb-5">banner Image</label>
                <div class="form-text">
                    Allowed file types: png, jpg,
                    jpeg.
                </div>
            </div>
            <input id="banner_image_remove_image1" type="hidden" name="banner_image_remove_image1" value="no">
            <div class="image-input image-input-outline manual-image1" data-kt-image-input="true"
                style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                @if ($info->banner_image ?? '')
                @php
                    $url = Storage::url($info->banner_image);
                    // print_r( $url );
                @endphp
                    <div class="image-input-wrapper w-125px h-125px manual-image1"
                        id="manual-image1"
                        style="background-image: url({{ asset($url) }});">
                    </div>
                @else
                    <div class="image-input-wrapper w-125px h-125px manual-image1"
                        id="manual-image1"
                        style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                    </div>
                @endif
                <label
                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                    title="Change Icon">
                    <i class="bi bi-pencil-fill fs-7"></i>
                    <input type="file" name="banner_image" id="readUrl1"
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
                    <i class="bi bi-x fs-2" id="avatar_remove_logo1"></i>
                </span>
            </div>
            <br>  <br>
            <label class="required fw-bold fs-6 mb-2" style="color:red;">The Banner Image must be at least 1600 x 265 pixels!</label>
    
        </div>
        
    </div>
</div>