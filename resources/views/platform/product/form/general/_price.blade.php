<div class="card-header">
    <div class="card-title">
        <h2>Pricing</h2>
    </div>
</div>
@php
    // dd( $info->productDiscount );
@endphp
<div class="card-body pt-0">
   <div class="fv-row row mb-10">
        <div class="col-md-4">
            <div class="">
                <label class="required form-label">Base Price</label>
                <input type="text" name="base_price"  onkeyup="getBaseMrpPrice('base_price', this.value)" id="base_price" class="form-control mb-2 mobile_num" placeholder="Product Price" value="{{ $info->price ?? '' }}" />
                <div class="text-muted fs-7">Set the product price.</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="">
                <label class="required form-label">MOP Price <small id="mrp_tax"> (Inclusive Tax (
                        {{ $info->productCategory->tax->pecentage ?? 0 }}%)) </small></label>
                <input type="text" name="mrp" id="mrp" onkeyup="getBaseMrpPrice('mrp', this.value)"
                    class="form-control mb-2 mobile_num" placeholder="Product Price"
                    value="{{ $info->mrp ?? '' }}" />
            </div>
        </div>
        <div class="col-sm-4">
            <div class="">
                <label class="required form-label">MRP Price <small id="mrp_tax"> (Inclusive Tax (
                        {{ $info->productCategory->tax->pecentage ?? 0 }}%)) </small></label>
                <input type="text" name="strike_price" id="strike_price" onkeyup="getDiscountPricePercentage('strike', this.value)"
                    class="form-control mb-2 mobile_num" placeholder="Product Price"
                    value="{{ $info->strike_price ?? '' }}" />
            </div>
        </div>
    </div>

    <div class="fv-row mb-10">
        <label class="fs-6 fw-bold mb-2">Discount Type
            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                title="Select a discount type that will be applied to this product"></i></label>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9" data-kt-buttons="true"
            data-kt-buttons-target="[data-kt-button='true']">
            <div class="col">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default active d-flex text-start p-6"
                    data-kt-button="true">
                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                        <input class="form-check-input" type="radio" name="discount_option" value="1"
                            @if (!isset($info->productDiscount)) checked="checked" @endif />
                    </span>
                    <span class="ms-5">
                        <span class="fs-4 fw-bolder text-gray-800 d-block">No Discount</span>
                    </span>
                </label>
            </div>
            <div class="col">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex text-start p-6"
                    data-kt-button="true">
                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                        <input class="form-check-input" type="radio" name="discount_option" value="percentage"
                            @if (isset($info->productDiscount->discount_type) && $info->productDiscount->discount_type == 'percentage') checked="checked" @endif />
                    </span>
                    <span class="ms-5">
                        <span class="fs-4 fw-bolder text-gray-800 d-block">Percentage %</span>
                    </span>
                </label>
            </div>
            <div class="col">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex text-start p-6"
                    data-kt-button="true">
                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                        <input class="form-check-input" type="radio" name="discount_option" value="fixed_amount"
                            @if (isset($info->productDiscount->discount_type) && $info->productDiscount->discount_type == 'fixed_amount') checked="checked" @endif />
                    </span>
                    <span class="ms-5">
                        <span class="fs-4 fw-bolder text-gray-800 d-block">Fixed Price</span>
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="@if (isset($info->productDiscount->discount_type) && $info->productDiscount->discount_type == 'percentage') @else d-none @endif mb-10 fv-row"
        id="kt_ecommerce_add_product_discount_percentage">
        <label class="form-label">Set Discount Percentage</label>
        <div class="d-flex flex-column text-center mb-5">
            <div class="d-flex align-items-start justify-content-center mb-7">
                <span class="fw-bolder fs-3x" id="kt_ecommerce_add_product_discount_label">0</span>
                <span class="fw-bolder fs-4 mt-1 ms-2">%</span>
            </div>
            <div id="kt_ecommerce_add_product_discount_slider" class="noUi-sm"></div>
            <input type="hidden" name="discount_percentage" id="discount_percentage">
        </div>
        <div class="text-muted fs-7">Set a percentage discount to be applied on this product.</div>
    </div>
    <div class="@if (isset($info->productDiscount->discount_type) && $info->productDiscount->discount_type == 'fixed_amount') @else d-none @endif mb-10 fv-row"
        id="kt_ecommerce_add_product_discount_fixed">
        <label class="form-label">Fixed Discounted Price</label>
        <input type="text" name="dicsounted_price" onkeyup="return getSalePrice(this)"
            class="form-control mb-2 mobile_num" placeholder="Discounted price"
            value="{{ $info->productDiscount->amount ?? '' }}" />
        <div class="text-muted fs-7">Set the discounted product price. The product will be reduced at the determined
            fixed price</div>
    </div>

    <div class="row mb-10 d-none" id="kt_ecommerce_add_product_sale_price">
        <div class="col-md-4">
            <div class="mb-10">
                <label class="form-label">Sale Price</label>
                <input type="text" name="sale_price" id="sale_price" class="form-control mb-2 mobile_num"
                    placeholder="Sale Price" value="{{ $info->sale_price ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Sale Start Date</label>
            <input id="kt_product_sale_start_date" name="sale_start_date" placeholder="Select a date"
                class="form-control mb-2" value="{{ $info->sale_start_date ?? '' }}" />
            <div class="fv-plugins-message-container invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Sale End Date</label>
            <input id="kt_product_sale_end_date" name="sale_end_date" placeholder="Select a date"
                class="form-control mb-2" value="{{ $info->sale_end_date ?? '' }}" />
            <div class="fv-plugins-message-container invalid-feedback"></div>
        </div>
    </div>
</div>

<script>
    function getBaseMrpPrice(inputField, price) {
        console.log(price, 'price');
        let category_id = $('#category_id').val();
        console.log(category_id, 'category_id');

        $.ajax({
            url: "{{ route('get.product.base_mrp_prce') }}",
            type: 'POST',
            data: {
                category_id: category_id,
                price: price,
                inputField: inputField
            },
            success: function(res) {
                if (res.error == 0) {
                    if (res.price_info.basePrice) {
                        $('#base_price').val(res.price_info.basePrice);
                    } else if (res.price_info.mrpPrice) {
                        $('#mrp').val(res.price_info.mrpPrice);
                    }
                } else {
                    toastr.error('Error', res.message);
                }
            }
        })
    }

    var discount_option = '{{ $info->productDiscount->discount_type ?? '' }}';
    var discount_percentage = '{{ $info->productDiscount->discount_value ?? 10 }}';

    const discountOptions = document.querySelectorAll('input[name="discount_option"]');
    const salePriceEL = document.getElementById('kt_ecommerce_add_product_sale_price');
    const percentageEl = document.getElementById('kt_ecommerce_add_product_discount_percentage');
    const fixedEl = document.getElementById('kt_ecommerce_add_product_discount_fixed');

    discountOptions.forEach(option => {
        option.addEventListener('change', e => {
            var value = e.target.value;

            switch (value) {
                case 'percentage': {
                    percentageEl.classList.remove('d-none');
                    salePriceEL.classList.remove('d-none');
                    fixedEl.classList.add('d-none');
                    break;
                }
                case 'fixed_amount': {
                    percentageEl.classList.add('d-none');
                    salePriceEL.classList.remove('d-none');
                    fixedEl.classList.remove('d-none');
                    break;
                }
                default: {
                    percentageEl.classList.add('d-none');
                    fixedEl.classList.add('d-none');
                    salePriceEL.classList.add('d-none');
                    break;
                }
            }
        });
    });

    if (discount_option == 'percentage') {
        percentageEl.classList.remove('d-none');
        salePriceEL.classList.remove('d-none');
        fixedEl.classList.add('d-none');
    } else if (discount_option == 'fixed_amount') {
        percentageEl.classList.add('d-none');
        salePriceEL.classList.remove('d-none');
        fixedEl.classList.remove('d-none');
    }


    const initSlider = () => {
        var slider = document.querySelector("#kt_ecommerce_add_product_discount_slider");
        var value = document.querySelector("#kt_ecommerce_add_product_discount_label");

        noUiSlider.create(slider, {
            start: [discount_percentage],
            connect: true,
            range: {
                "min": 1,
                "max": 100
            }
        });

        slider.noUiSlider.on("update", function(values, handle) {
            value.innerHTML = Math.round(values[handle]);
            var priceProduct = document.getElementById('mrp').value;
            var draggedInput = Math.round(values[handle]);
            priceProduct = parseFloat(priceProduct);

            var discount_amount = (priceProduct * (draggedInput / 100)).toFixed(2);
            var sale_price = priceProduct - discount_amount;

            document.getElementById('sale_price').value = sale_price;
            var discount = document.getElementById('discount_percentage');
            discount.value = Math.round(values[handle])
            if (handle) {
                value.innerHTML = Math.round(values[handle]);
            }
        });
    }

    function getSalePrice(dPrice) {

        var priceProduct = document.getElementById('mrp').value;
        if (dPrice.value == '') {
            document.getElementById('sale_price').value = priceProduct;
        } else {
            var fixedAmount = parseFloat(dPrice.value);
            var sale_price = priceProduct - fixedAmount;
            document.getElementById('sale_price').value = sale_price;
        }

    }
</script>