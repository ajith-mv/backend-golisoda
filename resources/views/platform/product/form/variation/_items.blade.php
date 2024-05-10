@if (isset($info->productVariationOption) && !empty($info->productVariationOption))
    @foreach ($info->productVariationOption as $attr)
    <div id="kt_docs_repeater_nested">
        <!--begin::Form group-->
        <div class="form-group">
            <div data-repeater-list="kt_docs_repeater_nested_outer">
                <div data-repeater-item>
                    <div class="form-group row mb-5">
                        <div class="col-md-3">
                            <label class="required form-label fs-6 mb-2">Variation</label>
                            <select class="form-select  product-attr-select required" name="variation_id"
                                data-control="select2" data-placeholder="Select an variation">
                                <option></option>
                                @foreach ($variations as $item)
                                    <option value="{{ $item->id }}" @if (isset($attr->variation_id) && $attr->variation_id == $item->id) selected @endif>{{ $item->title }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-7">
                            <div class="inner-repeater">
                                <div data-repeater-list="kt_docs_repeater_nested_inner" class="mb-5">
                                    <div data-repeater-item>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="required form-label fs-6 mb-2">Variation value</label>
                                                {{-- <select class="form-select product-attr-select1 required"
                                                name="variation_value" data-control="select2"
                                                data-placeholder="Select an variation value">
                                                <option></option>
                                            </select> --}}
                                                <input type="text" value="{{ $attr->value; }}" name="variation_value" class="form-control"
                                                    placeholder="Enter Variation value" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Amount:</label>
                                                <div class="input-group pb-3">
                                                    <input type="text" name="amount" value="{{ $attr->amount; }}" class="form-control"
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
            </div>
        </div>
        <!--end::Form group-->

        <!--begin::Form group-->
        <div class="form-group">
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
    <!--end::Repeater-->

    <script>
        setTimeout(() => {
            $('.product-attr-select').select2();
        }, 200);
        setTimeout(() => {
            $('.product-attr-select1').select2();
        }, 200);

        $('#kt_docs_repeater_nested').repeater({
            repeaters: [{
                selector: '.inner-repeater',
                show: function() {
                    $(this).slideDown();
                },
                hide: function(deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            }],
            show: function() {

                let row_length = $(this).closest('div').find('.inner-repeater').find(
                    "[data-repeater-list='kt_docs_repeater_nested_inner']").find('.repeater_div');

                if (row_length.length > 1) {

                    row_length.not(':first').remove();
                }

                $(this).slideDown();
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    </script>
    @endforeach
@else
    <div id="kt_docs_repeater_nested">
        <!--begin::Form group-->
        <div class="form-group">
            <div data-repeater-list="kt_docs_repeater_nested_outer">
                <div data-repeater-item>
                    <div class="form-group row mb-5">
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
                                            <div class="col-md-6">
                                                <label class="required form-label fs-6 mb-2">Variation value</label>
                                                {{-- <select class="form-select product-attr-select1 required"
                                                name="variation_value" data-control="select2"
                                                data-placeholder="Select an variation value">
                                                <option></option>
                                            </select> --}}
                                                <input type="text" name="variation_value" class="form-control"
                                                    placeholder="Enter Variation value" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Amount:</label>
                                                <div class="input-group pb-3">
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
            </div>
        </div>
        <!--end::Form group-->

        <!--begin::Form group-->
        <div class="form-group">
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
    <!--end::Repeater-->

    <script>
        setTimeout(() => {
            $('.product-attr-select').select2();
        }, 200);
        setTimeout(() => {
            $('.product-attr-select1').select2();
        }, 200);

        $('#kt_docs_repeater_nested').repeater({
            repeaters: [{
                selector: '.inner-repeater',
                show: function() {
                    $(this).slideDown();
                },
                hide: function(deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            }],
            show: function() {

                let row_length = $(this).closest('div').find('.inner-repeater').find(
                    "[data-repeater-list='kt_docs_repeater_nested_inner']").find('.repeater_div');

                if (row_length.length > 1) {

                    row_length.not(':first').remove();
                }

                $(this).slideDown();
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    </script>
@endif
