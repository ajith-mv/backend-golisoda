<!--begin::Card header-->
 <div class="card-header">
    <div class="card-title">
        <h2>Shipping</h2>
    </div>
</div>
<!--end::Card header-->
<!--begin::Card body-->
<div class="card-body pt-0">
    <!--begin::Input group-->
    <div class="fv-row">
        <!--begin::Input-->
        <div class="form-check form-check-custom form-check-solid mb-2">
            <input class="form-check-input" name="isShipping" type="checkbox" id="kt_ecommerce_add_product_shipping_checkbox" value="1"  @if(isset($info->productMeasurement) && !empty( $info->productMeasurement )) checked @endif/>
            <label class="form-check-label" for="kt_ecommerce_add_product_shipping_checkbox">This is a physical product</label>
        </div>
        <!--end::Input-->
        <!--begin::Description-->
        <div class="text-muted fs-7">Set if the product is a physical or digital item. Physical products may require shipping.</div>
        <!--end::Description-->
    </div>
    <!--end::Input group-->
    <!--begin::Shipping form-->
    <div id="kt_ecommerce_add_product_shipping" class="@if(isset($info->productMeasurement) && !empty( $info->productMeasurement ))  @else d-none @endif mt-10">
        <!--begin::Input group-->
        <div class="mb-10 fv-row">
            <!--begin::Label-->
            <label class="form-label">Weight</label>
            <!--end::Label-->
            <!--begin::Editor-->
            <input type="text" name="weight" class="form-control mb-2 mobile_num" placeholder="Product weight" value="{{ $info->productMeasurement->weight ?? '' }}" />
            <!--end::Editor-->
            <!--begin::Description-->
            <div class="text-muted fs-7">Set a product weight in kilograms (kg).</div>
            <!--end::Description-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="fv-row">
            <!--begin::Label-->
            <label class="form-label">Dimension</label>
            <!--end::Label-->
            <!--begin::Input-->
            <div class="d-flex flex-wrap flex-sm-nowrap gap-3">
                <input type="text" name="width" class="form-control mb-2 mobile_num" placeholder="Width (w)" value="{{ $info->productMeasurement->width ?? '' }}" />
                <input type="text" name="height" class="form-control mb-2 mobile_num" placeholder="Height (h)" value="{{ $info->productMeasurement->hight ?? '' }}" />
                <input type="text" name="length" class="form-control mb-2 mobile_num" placeholder="Length (l)" value="{{ $info->productMeasurement->length ?? '' }}" />
            </div>
            <!--end::Input-->
            <!--begin::Description-->
            <div class="text-muted fs-7">Enter the product dimensions in centimeters (cm).</div>
            <!--end::Description-->
        </div>
        <!--end::Input group-->
    </div>
    <!--end::Shipping form-->
</div>
<!--end::Card header-->

<script>
    const shippingOption = document.getElementById('kt_ecommerce_add_product_shipping_checkbox');
    const shippingForm = document.getElementById('kt_ecommerce_add_product_shipping');

    shippingOption.addEventListener('change', e => {
        const value = e.target.checked;

        if (value) {
            shippingForm.classList.remove('d-none');
        } else {
            shippingForm.classList.add('d-none');
        }
    });
</script>