<!--begin::Repeater-->
<div id="kt_docs_repeater_basic">
    <!--begin::Form group-->
    <div class="form-group">
        <div data-repeater-list="kt_docs_repeater_basic">

            @if (isset($info->vendorLocation) && count($info->vendorLocation) > 0)
                @foreach ($info->vendorLocation as $vendor_location)
                    <div class="mb-8" data-repeater-item>
                        <div class="form-group row mb-8">
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">Branch name</label>
                                <input type="text" name="branch_name" class="form-control  required"
                                    placeholder="Enter Branch name" value="{{ $vendor_location->branch_name }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">Address Line 1</label>
                                <input type="text" name="address_line_1" class="form-control  required"
                                    placeholder="Enter Address line1" value="{{ $vendor_location->address_line1 }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">Address Line 2</label>
                                <input type="text" name="address_line_2" class="form-control  required"
                                    placeholder="Enter Address line2" value="{{ $vendor_location->address_line2 }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">City</label>
                                <input type="text" name="city" class="form-control  required"
                                    placeholder="Enter City" value="{{ $vendor_location->city }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">State</label>
                                <input type="text" name="state" class="form-control  required"
                                    placeholder="Enter State" value="{{ $vendor_location->state }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">Pincode</label>
                                <input type="text" name="pincode" class="form-control  required"
                                    placeholder="Enter Pincode" value="{{ $vendor_location->pincode }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">Email</label>
                                <input type="email" name="email_id" class="form-control  required"
                                    placeholder="Enter Email" value="{{ $vendor_location->email_id }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">Mobile number</label>
                                <input type="text" name="mobile_number" class="form-control  required"
                                    placeholder="Enter Mobile number" value="{{ $vendor_location->mobile_no }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">Contact person</label>
                                <input type="text" name="contact_person" class="form-control  required"
                                    placeholder="Enter Contact Person" value="{{ $vendor_location->contact_person }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="required form-label fs-6 mb-2">Contact number</label>
                                <input type="text" name="contact_number" class="form-control  required"
                                    placeholder="Enter Contact Number" value="{{ $vendor_location->contact_number }}" />
                            </div>
                            <div class="col-md-2">
                                <label class=" form-label fs-6 mb-2">Is Default</label>
                                <div class="input-group pb-3" style="margin-top: 13px">
                                    <input type="checkbox" class="is-default-checkbox" name="is_default" value="{{ $vendor_location->is_default }}" @if($vendor_location->is_default == 1) checked @endif>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <a href="javascript:;" data-repeater-delete
                                    class="btn btn-sm btn-light-danger mt-3 mt-md-8">
                                    <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span><span
                                            class="path4"></span><span class="path5"></span></i>
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div data-repeater-item>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Branch name</label>
                            <input type="text" name="branch_name" class="form-control  required"
                                placeholder="Enter Branch name" value=""/>
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Address Line 1</label>
                            <input type="text" name="address_line_1" class="form-control  required"
                                placeholder="Enter Address line1" value="">
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Address Line 2</label>
                            <input type="text" name="address_line_2" class="form-control  required"
                                placeholder="Enter Address line2" value="">
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">City</label>
                            <input type="text" name="city" class="form-control  required"
                                placeholder="Enter City" value="" />
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">State</label>
                            <input type="text" name="state" class="form-control  required"
                                placeholder="Enter State" value="" />
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Pincode</label>
                            <input type="text" name="pincode" class="form-control  required"
                                placeholder="Enter Pincode" value=""/>
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Email</label>
                            <input type="text" name="email_id" class="form-control  required"
                                placeholder="Enter Email" value="" />
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Mobile number</label>
                            <input type="text" name="mobile_number" class="form-control  required"
                                placeholder="Enter Mobile number" value="">
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Contact person</label>
                            <input type="text" name="contact_person" class="form-control  required"
                                placeholder="Enter Contact Person" value="" />
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Contact number</label>
                            <input type="text" name="contact_number" class="form-control  required"
                                placeholder="Enter Contact Number" value="" />
                        </div>
                        <div class="col-md-2">
                            <label class=" form-label fs-6 mb-2">Is Default</label>
                            <div class="input-group pb-3" style="margin-top: 13px">
                                <input type="checkbox" class="is-default-checkbox" name="is_default" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <a href="javascript:;" data-repeater-delete
                                class="btn btn-sm btn-light-danger mt-3 mt-md-8">
                                <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span
                                        class="path2"></span><span class="path3"></span><span
                                        class="path4"></span><span class="path5"></span></i>
                                Delete
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!--end::Form group-->

    <!--begin::Form group-->
    <div class="form-group mt-5">
        <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
            <i class="ki-duotone ki-plus fs-3"></i>
            Add Location
        </a>
    </div>
    <!--end::Form group-->
</div>
<!--end::Repeater-->
<script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        repeater();
    });
    $('#kt_docs_repeater_basic').repeater({
        initEmpty: false,

        defaultValues: {
            'text-input': 'foo'
        },

        show: function() {
            $(this).slideDown();
            repeater()
        },

        hide: function(deleteElement) {
            $(this).slideUp(deleteElement);
        }
    });

    function repeater() {
        $("[data-repeater-list='kt_docs_repeater_basic']").closest('div').each(function() {
            console.log('works here');
            var $row = $(this);
            $row.find('.is-default-checkbox').click(function() {
                console.log('works inside click');
                $row.find('.is-default-checkbox').not(this).prop('checked', false);
                $row.addClass('required');
                $(this).val($(this).is(':checked') ? '1' : '0');
            });
        });
    }

    function classadd() {
        $("[data-repeater-list='kt_docs_repeater_basic']").closest('div').each(function() {
            var $row = $(this);
            $row.on("keyup", '.form-control.amount.required.error', function() {
                var value = $('.form-control.amount').val();
                $('.form-control.amount.required').css('width', '145px');
                $('.amount.border.border-secondary.btn.btn-icon.btn-flex.btn-light-danger').css({
                    'margin-top': '-42px',
                    'margin-left': '230px'
                });
            });
        });
    }
</script>
