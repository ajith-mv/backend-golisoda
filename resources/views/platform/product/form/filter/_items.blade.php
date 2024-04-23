@if (isset($info->productAttributes) && !empty($info->productAttributes))

    @foreach ($info->productAttributes as $attr)
        <div class="form-group d-flex flex-wrap gap-5 childRow">
            <!--begin::Select2-->
            <div class="w-100 w-md-200px">
                <select class="form-select product-attr-select required" name="filter_variation[]"
                    aria-label="Select a Attribute" data-control="select2" data-placeholder="Select a Attribute...">
                    <option></option>
                    @foreach ($attributes as $item)
                        <option value="{{ $item->id }}" @if (isset($attr->product_attribute_set_id) && $attr->product_attribute_set_id == $item->id) selected @endif>
                            {{ $item->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!--end::Select2-->
            <!--begin::Input-->
            <input type="text" class="form-control mw-100 w-250px required" name="filter_variation_title[]"
                value="{{ $attr->title ?? '' }}" placeholder="Title" required />
            <input type="text" class="form-control mw-100 w-300px required" name="filter_variation_value[]"
                value="{{ $attr->attribute_values ?? '' }}" placeholder="Values" required />
            <input type="text" class="form-control mw-100 w-100px required numberonly"
                name="filter_variation_order[]" value="{{ $attr->order_by ?? '' }}" maxlength="3" placeholder="Orders" required />

            <!--end::Input-->
            <input type="checkbox" name="is_overview_{{ $loop->iteration }}" value="1"
                @if (isset($attr->is_overview) && $attr->is_overview == 'yes') checked @endif>
            <button type="button" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger removeRow" onclick="return removeRow(this)">
                <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                <span class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none">
                        <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1"
                            transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
                        <rect x="8.46447" y="7.05029" width="12" height="2" rx="1"
                            transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
            </button>
        </div>
        <script>
            setTimeout(() => {
                $('.product-attr-select').select2();
            }, 200);
            $('.numberonly').keypress(function(e) {
                var charCode = (e.which) ? e.which : event.keyCode
                if (String.fromCharCode(charCode).match(/[^0-9]/g))
                    return false;
            });
        </script>
    @endforeach
@else
    <div class="form-group d-flex flex-wrap gap-5 childRow">
        <!--begin::Select2-->
        <div class="w-100 w-md-200px">
            <select class="form-select product-attr-select required" name="filter_variation[]"
                aria-label="Select a Attribute" data-control="select2" data-placeholder="Select a Attribute...">
                <option></option>
                @foreach ($attributes as $item)
                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                @endforeach
            </select>
        </div>
        <!--end::Select2-->
        <!--begin::Input-->
        <input type="text" class="form-control mw-100 w-250px required" name="filter_variation_title[]"
            placeholder="Title" required />
        <input type="text" class="form-control mw-100 w-300px required" name="filter_variation_value[]"
            placeholder="Values" required />
        <input type="text" class="form-control mw-100 w-100px required numberonly"
            name="filter_variation_order[]" value="" maxlength="3" placeholder="Orders" required />
        <input type="checkbox" name="is_overview_1" value="1" @if (isset($info->is_overview) && $info->is_overview == 'yes') checked @endif>
        <!--end::Input-->
        <button type="button" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger removeRow"
            onclick="return removeRow(this)">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
            <span class="svg-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none">
                    <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1"
                        transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
                    <rect x="8.46447" y="7.05029" width="12" height="2" rx="1"
                        transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </button>
    </div>
    <script>
        setTimeout(() => {
            $('.product-attr-select').select2();
        }, 200);

        $('.numberonly').keypress(function(e) {
            var charCode = (e.which) ? e.which : event.keyCode
            if (String.fromCharCode(charCode).match(/[^0-9]/g))
                return false;
        });
    </script>
@endif
