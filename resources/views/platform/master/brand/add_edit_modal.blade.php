<!--begin::Header-->
<div class="card-header" id="kt_activities_header">
    <h3 class="card-title fw-bolder text-dark">{{ $modal_title ?? 'Form Action' }}</h3>
    <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
            <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                        transform="rotate(-45 6 17.3137)" fill="currentColor" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                        transform="rotate(45 7.41422 6)" fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </button>
    </div>
</div>
<!--end::Header-->
<!--begin::Body-->
<form id="add_brand_form" class="form" action="#" enctype="multipart/form-data">

    <div class="card-body position-relative" id="kt_activities_body">
        <div id="kt_activities_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
            data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
            <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll">
                <div class="fv-row mb-10">
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                        data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header"
                        data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">

                        <input type="hidden" name="id" value="{{ $info->id ?? '' }}">
                        <input type="hidden" name="from" id="from" value="{{ $from ?? '' }}">

                        <div class="fv-row mb-7">
                            <div class="col-md-6">
                                <label class="required fw-bold fs-6 mb-2">Brand Name</label>
                                <input type="text" name="brand_name"
                                    class="form-control form-control-solid mb-3 mb-lg-0 required" placeholder="Brand Name"
                                    value="{{ $info->brand_name ?? '' }}" />
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold fs-6 mb-2">Minimum Shipping amount</label>
                                <input type="text" name="minimum_shipping_amount" id="minimum_shipping_amount"
                                    class="form-control form-control-solid mb-3 mb-lg-0"
                                    value="{{ $info->minimum_shipping_amount ?? '' }}">
                            </div>
                            
                        </div>
                        <div class="row mb-7">
                            <div class="col-md-6">

                                <div class="mb-7">
                                    <label class="d-block fw-bold fs-6 mb-5">Logo</label>
                                    <div class="form-text">
                                        Allowed file types: png, jpg,
                                        jpeg.
                                    </div>
                                </div>
                                <input id="image_remove_logo" type="hidden" name="image_remove_logo" value="no">
                                <div class="image-input image-input-outline manual-image-logo"
                                    data-kt-image-input="true"
                                    style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                                    @if ($info->brand_logo ?? '')
                                        @php
                                            $brandLogoPath = 'brands/' . $info->id . '/option1/' . $info->brand_logo;
                                            $url = Storage::url($brandLogoPath);
                                            $path = asset($url);
                                        @endphp
                                        <div class="image-input-wrapper w-125px h-125px manual-image-logo"
                                            id="manual-image-logo" style="background-image: url({{ asset($path) }});">
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
                                        <input type="file" name="brand_logo" id="readUrllogo"
                                            accept=".png, .jpg, .jpeg" />
                                        {{-- <input type="hidden" name="avatar_remove_logo" /> --}}
                                        {{-- <input type="file" name="userImage" id="userImage"> --}}
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
                            <div class="col-md-6">
                                <label class="fw-bold fs-6 mb-2">Short Description</label>
                                <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Short Description"
                                    name="short_description" id="short_description" cols="30" rows="2">{{ $info->short_description ?? '' }}</textarea>
                            </div>


                        </div>
                        <div class="row mb-7">
                            <div class="col-md-6">
                                <label class="required fw-bold fs-6 mb-2">Commission type</label>

                                <div class="form-check form-check-custom form-check-solid">
                                    <input name="commission_type" class="form-check-input required" {{ $info->commission_type == 'fixed' ? 'checked' : '' }} type="radio" value="fixed" id="fixed"/>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        Fixed
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid">
                                    <input name="commission_type" class="form-check-input required" type="radio" {{ $info->commission_type == 'percentage' ? 'checked' : '' }} value="percentage" id="percentage"/>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        Percentage
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="required fw-bold fs-6 mb-2">Commission value</label>
                                <input type="text" name="commission_value"
                                    class="form-control required form-control-solid mb-3 mb-lg-0" placeholder="Commission Value"
                                    value="{{ $info->commission_value ?? '' }}" />
                            </div>
                            
                           
                        </div>
                        <div class="row mb-7">
                            <div class="col-md-6">
                                <label class="fw-bold fs-6 mb-2">Notes</label>
                                <input type="text" name="notes" id="notes"
                                    class="form-control form-control-solid mb-3 mb-lg-0"
                                    value="{{ $info->notes ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="required fw-bold fs-6 mb-2">Sorting Order</label>
                                <input type="text" name="order_by"
                                    class="form-control required form-control-solid mb-3 mb-lg-0 mobile_num"
                                    placeholder="Sorting Order" value="{{ $info->order_by ?? '' }}" />
                            </div>
                        </div>

                        @include('platform.master.brand._items')

                        <div class="row mb-7">
                            <div class="col-md-4">
                                <label class="fw-bold fs-6 mb-2"> Status </label>
                                <div
                                    class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                    <input class="form-check-input" type="checkbox" name="status" value="1"
                                        @if ((isset($info->status) && $info->status == 'published') || !isset($info->status)) checked @endif />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold fs-6 mb-2"> Is Top Brand </label>
                                <div
                                    class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                    <input class="form-check-input" type="checkbox" name="top_brand" value="1"
                                        @if (isset($info->is_top_brand) && $info->is_top_brand == 'yes') checked @endif />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold fs-6 mb-2"> Is Free shipping </label>
                                <div
                                    class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_free_shipping"
                                        value="1" @if (isset($info->is_free_shipping) && $info->is_free_shipping == 1) checked @endif />
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-5 text-center" id="kt_activities_footer">
        <div class="text-end px-8">
            <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
            <button type="submit" id="kt_ecommerce_add_brand_submit" class="btn btn-primary"
                data-kt-order_status-modal-action="submit">
                <span class="indicator-label">Submit</span>
                <span class="indicator-progress">Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>
</form>

<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>

<script>
    $('.mobile_num').keypress(
        function(event) {
            if (event.keyCode == 46 || event.keyCode == 8) {
                //do nothing
            } else {
                if (event.keyCode < 48 || event.keyCode > 57) {
                    event.preventDefault();
                }
            }
        }
    );
    document.getElementById('readUrllogo').addEventListener('change', function() {

        if (this.files[0]) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
                console.log(event.target);
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
    //banner image script
    document.getElementById('readUrlbanner').addEventListener('change', function() {

        if (this.files[0]) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
                console.log(event.target);
                let img_url = event.target.result;
                $('#manual-image-banner').css({
                    'background-image': 'url(' + event.target.result + ')'
                });
            });
        }
    });
    document.getElementById('avatar_remove_banner').addEventListener('click', function() {
        $('#image_remove_banner').val("yes");
        $('#manual-image-banner').css({
            'background-image': ''
        });
    });
</script>

<script>
    $('#country').select2();
    $('.mobile_num').keypress(
        function(event) {
            if (event.keyCode == 46 || event.keyCode == 8) {
                //do nothing
            } else {
                if (event.keyCode < 48 || event.keyCode > 57) {
                    event.preventDefault();
                }
            }
        }
    );
    var add_url = "{{ route('brands.save') }}";

    // Class definition
    var KTUsersAddRole = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_brand_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);

        // Init add schedule modal
        var initAddRole = () => {

            $('#add_brand_form').validate({
                rules: {
                    brand_name: "required"
                },
                messages: {
                    brand_name: "Brand name is required",
                },

                submitHandler: function(form) {
                    var from = $('#from').val();
                    var formData = new FormData(document.getElementById(
                        "add_brand_form"));
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    // Disable button to avoid multiple click
                    submitButton.disabled = true;
                    //call ajax call
                    $.ajax({
                        url: add_url,
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {

                        },
                        success: function(res) {

                            if (res.error == 1) {
                                // Remove loading indication
                                submitButton.removeAttribute(
                                    'data-kt-indicator');
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

                                if (from != '') {
                                    getProductBrandDropdown(res.brand_id);
                                    return false;
                                }
                                dtTable.ajax.reload();
                                Swal.fire({
                                    text: res.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function(result) {
                                    if (result
                                        .isConfirmed) {
                                        commonDrawer
                                            .hide();

                                    }
                                });
                            }
                        }
                    });
                }
            });


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
                }).then(function(result) {
                    if (result.value) {
                        commonDrawer.hide(); // Hide modal				
                    }
                });
            });

            // Submit button handler
            const submitButton = element.querySelector('[data-kt-order_status-modal-action="submit"]');

        }

        return {
            // Public functions
            init: function() {
                initAddRole();
            }
        };
    }();

    // On document ready
    KTUtil.onDOMContentLoaded(function() {
        KTUsersAddRole.init();
    });

    $(document).ready(function() {
        $('#kt_ecommerce_add_brand_submit').click(function() {
            console.log('click function called');
            $("[data-repeater-list='kt_docs_repeater_basic']").closest('div').each(function() {
                console.log('closest div called');
                var $row = $(this);
                var $checkbox = $row.find('.is-default-checkbox:first');
                var $checkboxes = $row.find('.is-default-checkbox');
                var hasChecked = false;
                $checkboxes.each(function() {
                    if ($(this).prop('checked')) {
                        hasChecked = true;
                        return false;
                    }
                });
                if (!hasChecked) {
                    $checkbox.addClass('required');
                } else {
                    $checkbox.removeClass('required');
                }

                // Apply CSS styles to other elements within the row
                $row.find('.form-control.amount.required').css('width', '145px');
                $row.find(
                    '.amount.border.border-secondary.btn.btn-icon.btn-flex.btn-light-danger'
                ).css({
                    'margin-top': '-61px',
                    'margin-left': '230px'
                });
            });
        });
    });
</script>
