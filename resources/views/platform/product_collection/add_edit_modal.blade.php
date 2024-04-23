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
<form id="add_product_attribute_form" class="form" action="#" enctype="multipart/form-data">

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
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="required fw-bold fs-6 mb-2">Collection Name</label>
                                    <input type="text" name="collection_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Collection Name" value="{{ $info->collection_name ?? '' }}" />
                                        <br>
                                    <label class=" fw-bold fs-6 mb-2">Tagline</label>
                                    <input type="text" name="tag_line" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Tag Line" value="{{ $info->tag_line ?? '' }}" />
                                </div>
                                <div class="col-md-6">
                                    <div class="fv-row mb-7">
                                        <label class="d-block fw-bold fs-6 mb-5">Image</label>
                                        <div class="form-text">Image size: 270 x 270.</div>
                                        <div class="form-text">Allowed file types: png, jpg,
                                            jpeg.</div>
                                    </div>
                                    <input id="image_remove_image" type="hidden" name="image_remove_image" value="no">
                                    <div class="image-input image-input-outline manual-image" data-kt-image-input="true"
                                        style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                                        @if ($info->image ?? '')
                                        @php
                                                $path = Storage::url($info->image)
                                        @endphp
                                            <div class="image-input-wrapper w-125px h-125px manual-image"
                                                id="manual-image"
                                                style="background-image: url({{ asset($path) }});">
                                                
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
                                            title="Change avatar">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <input type="file" name="image" id="readUrl"
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
                            
                        </div>
                        {{-- <div class="fv-row mb-7">
                           
                        </div> --}}

                        <div class="fv-row mb-7">
                            <label class=" fw-bold fs-6 mb-2">Products  (Minimum 5)</label>
                            <select name="collection_product[]" id="collection_product" aria-label="Select a Product" multiple="multiple" data-placeholder="Select a Product..." class="form-select mb-2" required>
                                <option value=""></option>
                                @isset($products)
                                    @foreach ($products as $item)
                                    <option value="{{ $item->id }}"  @if( isset($info->collectionProducts) && in_array( $item->id, array_column( $info->collectionProducts->toArray(), 'product_id'))  ) selected="selected" @endif>
                                        {{-- <option value="{{ $item->id }}" > --}}
                                            {{ $item->product_name }} {{ $item->sku }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
            
                        </div>
                        
                        <div class="row mb-7">
                            <div class="col-md-3">
                                <div class="mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Show as Discount Collection </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox"  name="can_map_discount" value="yes"  @if(isset( $info->can_map_discount) && $info->can_map_discount == 'yes') checked @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Handpicked Collection </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox"  name="is_handpicked_collection" value="yes"  @if(isset( $info->is_handpicked_collection) && $info->is_handpicked_collection == 'yes') checked @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Show on Home Page </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox"  name="show_home_page" value="yes"  @if(isset( $info->show_home_page) && $info->show_home_page == 'yes') checked @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Published </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox"  name="status" value="1"  @if(isset( $info->status) && $info->status == 'published') checked @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Sorting Order</label>
                                    <input type="text" name="order_by" id="order_by" class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                        placeholder="Sorting Order" value="{{ $info->order_by ?? '' }}" />
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
            <button type="submit" class="btn btn-primary" data-kt-order_status-modal-action="submit">
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
<script>

$(document).ready(function() {
        $.fn.select2.amd.require(['select2/selection/search'], function(Search) {
            var oldRemoveChoice = Search.prototype.searchRemoveChoice;

            Search.prototype.searchRemoveChoice = function() {
                oldRemoveChoice.apply(this, arguments);
                this.$search.val('');
            };
            $('#collection_product').select2();
        });
    });
   
    document.getElementById('readUrl').addEventListener('change', function() {
    
      if (this.files[0]) {
          var picture = new FileReader();
          picture.readAsDataURL(this.files[0]);
          picture.addEventListener('load', function(event) {
              console.log(event.target);
              let img_url = event.target.result;
              $('#manual-image').css({
                  'background-image': 'url(' + event.target.result + ')'
              });
          });
      }
  });
  document.getElementById('avatar_remove_logo').addEventListener('click', function() {
      $('#image_remove_image').val("yes");
      $('#manual-image').css({
          'background-image': ''
      });
  });
</script>
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
    var add_url = "{{ route('product-collection.save') }}";

    // Class definition
    var KTUsersAddRole = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_product_attribute_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);


        // Init add schedule modal
        var initAddRole = () => {

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'collection_name': {
                            validators: {
                                notEmpty: {
                                    message: 'Collection is required'
                                }
                            }
                        },
                        'collection_product': {
                            validators: {
                                notEmpty: {
                                    message: 'Products is required'
                                }
                            }
                        },
                        'order_by': {
                            validators: {
                                notEmpty: {
                                    message: 'Sorting Order is required'
                                }
                            } 
                        }
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
                }).then(function(result) {
                    if (result.value) {
                        commonDrawer.hide(); // Hide modal				
                    }
                });
            });

            // Submit button handler
            const submitButton = element.querySelector('[data-kt-order_status-modal-action="submit"]');
            // submitButton.addEventListener('click', function(e) {
            $('#add_product_attribute_form').submit(function(e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {

                            var formData = new FormData(document.getElementById("add_product_attribute_form"));
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            // Disable button to avoid multiple click 
                            submitButton.disabled = true;
                            var from = $('#from').val();
                            //call ajax call
                            $.ajax({
                                url: add_url,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                beforeSend: function() {},
                                success: function(res) {
                                    console.log( res.views );
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
                                        if( !from ) {
                                            dtTable.ajax.reload();
                                        }
                                        
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
                                                commonDrawer.hide();
                                                if( res.from ) {
                                                }

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

</script>
