<div class="card-body pt-0">
    <div class="mb-10 fv-row">
        <label class="required form-label">Product Name</label>
        <input type="text" name="product_name" class="form-control mb-2" placeholder="Product name" value="{{ $info->product_name ?? '' }}" />
        <div class="text-muted fs-7">A product name is required and recommended to be unique.</div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="mb-10 fv-row">
                <label class="form-label required">SKU</label>
                <input type="text" name="sku" class="form-control mb-2" placeholder="Sku" value="{{ $info->sku ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-10 fv-row">
                <label class="form-label">HSN Code</label>
                <input type="text" name="hsn_code" class="form-control mb-2" placeholder="HSN Code" value="{{ $info->hsn_code ?? '' }}" />
            </div>
        </div>
    </div>
    <div class="row" >
        <div class="col">
            <div class="col-md-3">
                <label class="required fs-6 fw-bold mb-2">Quantity</label>
                <input type="number" class="form-control form-control-solid numberonly" name="qty" value="{{ $info->quantity ?? '' }}" placeholder="Quantity" id="qty" min="1" max="1000" />
            </div>
        </div>
    </div>
    <br>
    
    
</div>