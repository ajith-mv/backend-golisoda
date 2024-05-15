    <div id="kt_docs_repeater_nested">
        <!--begin::Form group-->
        <div class="form-group">
            <div data-repeater-list="kt_docs_repeater_nested_outer">
                @php
                $count =count($variations);
                @endphp
                <input type="hidden" name="count" value="{{$count ?? ''}}">
                @if (isset($info->productVariationOption) && count($info->productVariationOption) > 0)
                @php
                   $tempIds = [];
                   $tempArr = [];
                   foreach ($info->productVariationOption as $value) {
                        if (!in_array($value->variation_id, $tempIds)) {
                            $tempIds[] = $value->variation_id;
                        }
                   }
                   foreach ($tempIds as $Id) {
                    $found = array_filter($info->productVariationOption->toArray(), function($item) use ($Id) {
                        return $item['variation_id'] === $Id;
                    });
                    $tempArr[$Id] = $found;
                   }
                @endphp
                @foreach ($tempArr as $key => $attr)
                <div data-repeater-item>
                    <div class="form-group row mb-5 test">
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Variation</label>
                            <select class="form-select  product-attr-select required" name="variation_id"
                                data-control="select2" data-placeholder="Select an variation">
                                <option></option>
                                @foreach ($variations as $item)
                                    <option value="{{ $item->id }}" @if (isset($key) && $key == $item->id) selected @endif>{{ $item->title }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-7">
                            <div class="inner-repeater">
                                <div data-repeater-list="kt_docs_repeater_nested_inner" class="mb-5">
                                    @foreach ($attr as  $data)
                                    <div data-repeater-item class="repeater_div">
                                        <div class="row" >
                                            <div class="col-md-4">
                                                <label class="required form-label fs-6 mb-2">Variation value</label>
                                                <input type="text" value="{{ $data['value'] }}" name="variation_value" class="form-control"
                                                    placeholder="Enter Variation value" />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Amount:</label>
                                                <div class="input-group pb-3">
                                                    <input type="text" name="amount" value="{{ $data['amount'] }}" class="form-control"
                                                        placeholder="Enter Amount" />
                                                    <button
                                                        class="border border-secondary btn btn-icon btn-flex btn-light-danger"
                                                        data-repeater-delete type="button">
                                                        <i class="fa fa-trash"><span class="path1"></span><span
                                                                class="path2"></span><span class="path3"></span><span
                                                                class="path4"></span><span class="path5"></span></i>
                                                    </button>
                                        
                                                </div>

                                            </div>
                                            <div class="col-md-2">
                                                <label class=" form-label fs-6 mb-2">Is Default</label>
                                                    <div class="input-group pb-3" style="margin-top: 13px">
                                                        <input   type="checkbox" class="is-default-checkbox" name="is_default" value="{{$data['is_default'] ?? ''}}" @if (isset($data['is_default']) && $data['is_default'] == 1) checked @endif>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-sm btn-flex btn-light-primary" data-repeater-create
                                    type="button">
                                    <span class="svg-icon svg-icon-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="11" y="18" width="12" height="2"
                                                rx="1" transform="rotate(-90 11 18)" fill="currentColor" />
                                            <rect x="6" y="11" width="12" height="2" rx="1"
                                                fill="currentColor" />
                                        </svg>
                                        Add variation value
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="javascript:;" data-repeater-delete
                                class="btn btn-sm btn-flex btn-light-danger mt-3 mt-md-9">
                                <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span
                                        class="path2"></span><span class="path3"></span><span
                                        class="path4"></span><span class="path5"></span></i>
                                Delete Row
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
        @else
                <div data-repeater-item >
                    <div class="form-group row mb-5 test">
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Variation</label>
                            <select class="form-select  product-attr-select required" name="variation_id"
                                data-control="select2" data-placeholder="Select an variation">
                                <option></option>
                                @foreach ($variations as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-7">
                            <div class="inner-repeater">
                                <div data-repeater-list="kt_docs_repeater_nested_inner" class="mb-5">
                                    <div data-repeater-item>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="required form-label fs-6 mb-2">Variation value</label>
                                                <input type="text" name="variation_value" class="form-control"
                                                    placeholder="Enter Variation value" />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Amount:</label>
                                                <div class="input-group pb-6">
                                                    <input type="text" name="amount" class="form-control"
                                                        placeholder="Enter Amount" />
                                                    <button
                                                        class="border border-secondary btn btn-icon btn-flex btn-light-danger"
                                                        data-repeater-delete type="button">
                                                        <i class="fa fa-trash"><span class="path1"></span><span
                                                                class="path2"></span><span class="path3"></span><span
                                                                class="path4"></span><span class="path5"></span></i>
                                                    </button>
                                                 
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class=" form-label fs-6 mb-2">Is Default</label>
                                                    <div class="input-group pb-3" style="margin-top: 13px">
                                                        <input type="checkbox" class="is-default-checkbox" name="is_default" value="0">
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-flex btn-light-primary" data-repeater-create
                                    type="button">
                                    <span class="svg-icon svg-icon-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="11" y="18" width="12" height="2"
                                                rx="1" transform="rotate(-90 11 18)" fill="currentColor" />
                                            <rect x="6" y="11" width="12" height="2" rx="1"
                                                fill="currentColor" />
                                        </svg>
                                        Add variation value
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="javascript:;" data-repeater-delete
                                class="btn btn-sm btn-flex btn-light-danger mt-3 mt-md-9">
                                <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span
                                        class="path2"></span><span class="path3"></span><span
                                        class="path4"></span><span class="path5"></span></i>
                                Delete Row
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <!--end::Form group-->
        <!--begin::Form group-->
        <div class="form-group" id="adddiv">
            <a href="javascript:;" data-repeater-create class="btn btn-flex btn-light-primary">
                <span class="svg-icon svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="11" y="18" width="12" height="2" rx="1"
                            transform="rotate(-90 11 18)" fill="currentColor" />
                        <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor" />
                    </svg>
                    Add variation
                </span>
            </a>
        </div>
        <!--end::Form group-->
    </div>
<script>
$(document).ready(function() {
     repeter() ;
     length();
});
function length() {
    var length = $('.test').length; 
    var data = parseInt($('input[name="count"]').val()); 
        if (data >= length) {
            if(data === length){
                $('#adddiv').hide();
            }
            return true;
        } else {
            $('#adddiv').hide();
            return false;
        } 
}
function repeter() {
    $("[data-repeater-list='kt_docs_repeater_nested_inner']").closest('div').each(function() {
        var $row = $(this);
        $row.find('.is-default-checkbox').click(function() {
            $row.find('.is-default-checkbox').not(this).prop('checked', false);
            $(this).val($(this).is(':checked') ? '1' : '0');
        });
    });
 }
    setTimeout(() => {
        $('.product-attr-select').select2();
    }, 200);
    $('#kt_docs_repeater_nested').repeater({
    repeaters: [{
        selector: '.inner-repeater',
        show: function() {
            repeter();
            $(this).slideDown();
        },
        hide: function(deleteElement) {
            $(this).slideUp(deleteElement);
        }
    }],
    show: function() {
    repeter();
    let row_length = $(this).closest('div').find('.inner-repeater').find("[data-repeater-list='kt_docs_repeater_nested_inner']").find('.repeater_div');     
    if (row_length.length > 1) {
        row_length.not(':first').remove();
    }
    if (length()) {
        $(this).slideDown();
        setTimeout(() => {
        $('.product-attr-select').select2();
    }, 200);
    }
},
    hide: function(deleteElement) {
        $(this).slideUp(deleteElement);
        $('#adddiv').show(); 
    }
});
</script>