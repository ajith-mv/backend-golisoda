<!--begin::Variations-->
<div class="card card-flush py-4">
    <!--begin::Card header-->
    <div class="card-header">
        <div class="card-title w-100">
            <h2 class="w-100">
                Filter Variations
                <div class="form-group mt-5 float-end">
                    <button type="button" data-repeater-create="" onclick="return openForm('product-attribute', '', 'product')" class="btn btn-sm btn-light-primary">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="11" y="18" width="12" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor" />
                            <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor" />
                        </svg>
                    </span>
                    Add Product Attribute Set</button>
                </div>
            </h2>
        </div>
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Input group-->
        <div class="" data-kt-ecommerce-catalog-add-product="auto-options">
            <!--begin::Label-->
            <label class="form-label">Add Product Filter Variations</label>
            <!--end::Label-->
            <!--begin::Repeater-->
            <div id="kt_ecommerce_add_product_options">
                <!--begin::Form group-->
                <div class="form-group">
                    <div class="d-flex flex-column gap-3" id="formRepeaterId">
                    </div>
                </div>
                <!--end::Form group-->
                <!--begin::Form group-->
                <div class="form-group mt-5">
                    <button type="button" class="btn btn-sm btn-light-primary" onclick="return addVariationRow()">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr087.svg-->
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="11" y="18" width="12" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor" />
                            <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->Add another variation</button>
                </div>
                <!--end::Form group-->
            </div>
            <!--end::Repeater-->
        </div>
        <!--end::Input group-->
    </div>
    <!--end::Card header-->
</div>
<!--end::Variations-->
