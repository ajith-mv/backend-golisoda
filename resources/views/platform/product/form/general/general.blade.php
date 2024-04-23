<div class="d-flex flex-column gap-7 gap-lg-10">
    <!--begin::General options-->
    <input type="hidden" name="product_page_type" id="product_page_type" value="general">
    <div class="card card-flush py-4" id="product-info">
        @include('platform.product.form.general._product_info')
    </div>
    <!--end::General options-->
    <!--begin::Media-->
    <div class="card card-flush py-4" id="product-media">
        @include('platform.product.form.general._media')
    </div>
    <!--end::Media-->
    <!--begin::Pricing-->
    <div class="card card-flush py-4" id="product-price">
        @include('platform.product.form.general._price')
    </div>
    <!--end::Pricing-->
     <!--begin::Shipping-->
    <div class="card card-flush py-4" id="product-shipping">
        @include('platform.product.form.general._shipping')
    </div>
    <!--end::Shipping-->
</div>