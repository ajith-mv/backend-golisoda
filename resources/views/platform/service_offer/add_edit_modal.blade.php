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
<form id="add_service_offer_form" class="form" action="#" enctype="multipart/form-data">

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

                        <input type="hidden" name="id" value="{{ $info[0]->service_center_id ?? '' }}">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="required fw-bold fs-6 mb-2">Service Center</label>
                                <select name="service_center_id" id="service_center_id"
                                    aria-label="Select a service center" data-control="select2"
                                    data-placeholder="Select a service center..." class="form-select mb-2" required>
                                    <option value="">Select service center</option>
                                    @isset($serviceCenter)
                                        @foreach ($serviceCenter as $item)
                                            <option value="{{ $item->id }}"
                                                @if (isset($info[0]->service_center_id) && $info[0]->service_center_id == $item->id) selected @endif>{{ $item->title }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="col-sm-8  text-end p-11">
                                    <button id="rowAddon" type="button" class="btn btn-info">
                                        <span class="bi bi-plus-square-dotted">
                                        </span> ADD New Row
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="fv-row mb-7">
                            <div class="row">

                                @if (isset($info) && !empty($info))

                                    <?php  for($i = 0 ; $i < count($info) ; $i++){  ?>

                                    <div id="row" class="row p-7">

                                        <div class="col-sm-6">
                                            <input type="text" name="title[]" class="form-control"
                                                value="{{ $info[$i]->title ?? '' }}">
                                        </div>
                                        <input type="hidden" name="offer_id[]" value="{{ $info[$i]->id ?? '' }}">
                                        <div class="col-sm-3">
                                            <input type="file" name="image[]" class="mt-3"
                                                value="{{ $info[$i]->image ?? '' }}">
                                                @php
                                                $path = Storage::url($info[$i]->image, 'public');
                                            @endphp
    
                                            @if (!empty($info[$i]->image))
                                                <div class="">
                                                    <img src="{{ asset($path) }}" alt="" style="width:50px">
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-sm-2">
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-danger btn-sm" id="DeleteRowAddon"
                                                        type="button">
                                                        <i class="bi bi-trash"></i>
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php } ?>

                                @endif

                                <div id="newinputAddon"></div>
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
            <button type="submit" class="btn btn-primary" id="offer-btn">
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
    $('#service_center_id').select2();


    $("#rowAddon").on("click", function() {
        newRowAdd =
            '<div id="row" class="row p-7">' +
            '<div class="col-sm-6">' +
            '<input type="text" name="title[]" class="form-control" placeholder="Title" required>' +
            '</div>' +
            '<div class="col-sm-3 ">' +
            '<input type="file" name="image[]" class="mt-3" accept="image/*" required>' +
            '</div>' +
            '<div class="col-sm-2">' +
            '<div class="input-group mt-1">' +
            '<div class="input-group-prepend">' +
            '<button class="btn btn-danger btn-sm" id="DeleteRowAddon" type="button">' +
            '<i class="bi bi-trash"></i>' +
            'Delete' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

        $('#newinputAddon').append(newRowAdd);
    })
    $("body").on("click", "#DeleteRowAddon", function() {
        $(this).parents("#row").remove();
    })


    var add_url = "{{ route('service-offer.save') }}";

    var offerForm = document.getElementById('add_service_offer_form');
    var drawerEl = document.querySelector("#kt_common_add_form");
    var commonDrawer = KTDrawer.getInstance(drawerEl);


    $("#add_service_offer_form").submit(function(e) {
        e.preventDefault();

        var formData = new FormData(offerForm);
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
    });
</script>
