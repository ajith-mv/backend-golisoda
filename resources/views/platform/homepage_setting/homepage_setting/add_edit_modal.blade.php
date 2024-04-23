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
<form id="add_homepage_setting_form" class="form" action="#" enctype="multipart/form-data">

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
                        <div class="fv-row mb-7">
                            <div class="row">

                                <div class="col-sm-6">
                                    <label class="required fw-bold fs-6 mb-2">Title</label>
                                    <input type="text" name="title"
                                        class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Title"
                                        value="{{ $info->title ?? '' }}" />
                                </div>
                                <div class="col-sm-6">
                                    <label class="required fw-bold fs-6 mb-2">Backgroun Color</label>
                                    <input type="color" name="color"
                                        class="form-control form-control-solid mb-3 mb-lg-0"
                                        value="{{ $info->color ?? '' }}" />
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="fs-6 fw-bold mb-2">
                                <span class="required">State</span>
                            </label>
                            <select name="homepage_setting_field_id" id="homepage_setting_field_id"
                                aria-label="Select a Homepage Setting Field"
                                class="form-select form-select-solid fw-bolder">
                                <option value="">Select a Homepage setting field...</option>
                                @foreach ($field as $key => $val)
                                    <option data-id="{{ $val->title }}" value="{{ $val->id }}"
                                        @if (isset($info->homepage_setting_field_id) && $info->homepage_setting_field_id == $val->id) selected @endif>{{ $val->title }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Description</label>
                            <textarea class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Description" name="description"
                                id="description" cols="3" rows="2">{{ $info->description ?? '' }}</textarea>

                        </div>
                        <div class="fv-row mb-7">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="fw-bold fs-6 mb-2">Sorting Order</label>
                                    <input type="text" name="order_by"
                                        class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                        placeholder="Sorting Order" value="{{ $info->order_by ?? '' }}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold fs-6 mb-2"> Status </label>
                                    <div
                                        class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox" name="status"
                                            value="1" @if ((isset($info->status) && $info->status == 'published') || !isset($info->status)) checked @endif />
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($home_items_first)

                            <div class="fv-row mb-7">
                                @foreach ($home_items as $home_set_items)
                                    <div class="row mt-6" id='new_row_add'>
                                        <div class="col-md-3">
                                            <label class="fw-bold fs-6 mb-2 start_val" id=""> </label>
                                            <input type="text" id="start" name="start[]"
                                                class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                                placeholder="Start" value='{{ $home_set_items->start_size }}' />
                                        </div>
                                        <input type="hidden" name="item_id[]"
                                            value="{{ $home_set_items->id ?? '' }}">
                                        <div class="col-md-3">
                                            <label class="fw-bold fs-6 mb-2 end_val" id=""> </label>
                                            <input type="text" id="end" name="end[]"
                                                class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                                placeholder="End" value='{{ $home_set_items->end_size }}' />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fw-bold fs-6 mb-2"> Image </label>
                                            <input type="file" id="home_image" name="home_image[]"
                                                class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                                placeholder="" />

                                        </div>
                                        <div class="col-md-3">
                                            @if ($home_set_items->setting_image_name ?? '')
                                                @php
                                                    $path = Storage::url($home_set_items->setting_image_name);
                                                @endphp

                                                <img src="{{ asset($path) }}" width="75" alt="">
                                            @else
                                                <img src="{{ asset('userImage/no_Image.jpg') }}" width="75"
                                                    alt="">
                                            @endif
                                        </div>
                                @endforeach
                                <div class="col-md-3">
                                    <button type="button" id="btnAdd" class="btn btn-sm btn-light-primary mt-9">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr087.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="11" y="18" width="12"
                                                    height="2" rx="1" transform="rotate(-90 11 18)"
                                                    fill="currentColor" />
                                                <rect x="6" y="11" width="12" height="2"
                                                    rx="1" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->Add New
                                    </button>

                                </div>
                            </div>

                            <div id="newinput"></div>


                    </div>
                @else
                    <div class="fv-row mb-7">
                        <div class="row mt-6" id='new_row_add'>
                            <div class="col-md-3">
                                <label class="fw-bold fs-6 mb-2 start_val" id=""> </label>
                                <input type="text" id="start" name="start[]"
                                    class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                    placeholder="Start" />
                            </div>
                            <div class="col-md-3">
                                <label class="fw-bold fs-6 mb-2 end_val" id=""> </label>
                                <input type="text" id="end" name="end[]"
                                    class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                    placeholder="End" />
                            </div>
                            <div class="col-md-3">
                                <label class="fw-bold fs-6 mb-2"> Image </label>
                                <input type="file" id="home_image" name="home_image[]"
                                    class="form-control form-control-solid mb-3 mb-lg-0 mobile_num" placeholder="" />

                            </div>
                            <div class="col-md-3">
                                <button type="button" id="btnAdd" class="btn btn-sm btn-light-primary mt-9">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr087.svg-->
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="11" y="18" width="12"
                                                height="2" rx="1" transform="rotate(-90 11 18)"
                                                fill="currentColor" />
                                            <rect x="6" y="11" width="12" height="2"
                                                rx="1" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->Add New
                                </button>

                            </div>
                        </div>
                        <div id="newinput"></div>


                    </div>
                    @endif

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
        var status = $("#homepage_setting_field_id").val();
        if (status) {
            var state = $('select[name=homepage_setting_field_id]').find('option:selected').data('id');
            $(".start_val").html('Start ' + state);
            $(".end_val").html('End ' + state);
            $(".start_val_append").html('Start ' + state);
            $(".end_val_append").html('End ' + state);
        }
    });
    $('select').change(function() {
        var state = $(this).children('option:selected').data('id');
        $(".start_val").html('Start ' + state);
        $(".end_val").html('End ' + state);
    });

    $("#btnAdd").click(function() {
        var state = $('select[name=homepage_setting_field_id]').find('option:selected').data('id');
        //var state= $(this).children('option:selected').data('id');
        var status = $("#homepage_setting_field_id").val();
        if (status) {
            $(".start_val_append").html('Start ' + state);
            $(".end_val_append").html('End ' + state);
        }
        newRowAdd =
            ` <div class="row mt-6" id='new_row_add'>
                                <div class="col-md-3">
                                    <label class="fw-bold fs-6 mb-2 start_val_append start_val" > </label>
                                    <input type="text" id="start" name="start[]" class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                    placeholder="Start" />
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bold fs-6 mb-2 end_val_append end_val" >  </label>
                                    <input type="text" id="end" name="end[]" class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                    placeholder="End"  />
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bold fs-6 mb-2"> Image </label>
                                    <input type="file" id="home_image" name="home_image[]" class="form-control form-control-solid mb-3 mb-lg-0 mobile_num"
                                    placeholder="Sorting Order" />
                                    
                                </div>
                                <div class="col-md-3">
                                <button type="button" id="DeleteRow" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger removeRow mt-10" >
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor"></rect>
                                                <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor"></rect>
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </button>
                                </div>
                                    </div>`;

        $('#newinput').append(newRowAdd);
    });
    $("body").on("click", "#DeleteRow", function() {
        $(this).parents("#new_row_add").remove();

    })

    $('#homepage_setting_field_id').select2();

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

    var add_url = "{{ route('homepage-setting.save') }}";

    // Class definition
    var KTUsersAddHomepageSetting = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_homepage_setting_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);

        // Init add schedule modal
        var initAddHomepageSetting = () => {

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'title': {
                            validators: {
                                notEmpty: {
                                    message: 'Title is required'
                                }
                            }
                        },

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
            $('#add_homepage_setting_form').submit(function(e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {
                            var from = $('#from').val();
                            var formData = new FormData(document.getElementById(
                                "add_homepage_setting_form"));
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
                initAddHomepageSetting();
            }
        };
    }();

    // On document ready
    KTUtil.onDOMContentLoaded(function() {
        KTUsersAddHomepageSetting.init();
    });
</script>
