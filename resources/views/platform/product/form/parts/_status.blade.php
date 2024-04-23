 <div class="card-header">
    <div class="card-title">
        <h3 class="required">Status</h3>
    </div>
    <div class="card-toolbar">
        <div class="rounded-circle bg-success w-15px h-15px" id="kt_ecommerce_add_product_status"></div>
    </div>
</div>

<div class="card-body pt-0">
    @php
        $status_array = array('published', 'unpublished');
    @endphp
    <!--begin::Select2-->
    <select name="status" class="form-select mb-2 required" data-control="select2" data-hide-search="true" data-placeholder="Select an option" id="kt_ecommerce_add_product_status_select">
        <option value="">--select--</option>
        @if( isset( $status_array ) && !empty( $status_array )) 
            @foreach ($status_array as $item)
                <option value="{{ $item }}" @if( (isset( $info->status) && $info->status == $item) || (!isset($info->status) && $item == 'published') ) selected @endif >{{ ucfirst($item) }}</option>    
            @endforeach
        @endif
    </select>
    <div class="text-muted fs-7">Set the product status.</div>
</div>

<div class="w-100 border-top">
    <div class="row px-10 mt-5 mb-5">
        <div class="col-sm-8">
            <h3>Is Featured? </h3>
        </div>
        <div class="col-sm-4">
            <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                <input class="form-check-input" type="checkbox"  name="is_featured" value="1"  @if(isset( $info->is_featured) && $info->is_featured == '1') checked @endif />
            </div>
        </div>
    </div>
</div>

<div class="card-header border-top">
    <div class="card-title">
        <h3 class="required">Stock Status</h3>
    </div>
    <div class="card-toolbar">
        <div class="rounded-circle bg-success w-15px h-15px" id="kt_ecommerce_add_product_status"></div>
    </div>
</div>

<div class="card-body pt-0 ">
    @php
        $stock_status_array = array('in_stock', 'out_of_stock', 'coming_soon', 'notify');
    @endphp
    <!--begin::Select2-->
    <select name="stock_status" class="form-select mb-2 required" data-control="select2" data-hide-search="true" data-placeholder="Select an option" id="kt_ecommerce_add_product_status_select">
        @if( isset( $stock_status_array ) && !empty( $stock_status_array )) 
            @foreach ($stock_status_array as $item)
                <option value="{{ $item }}" @if( (isset( $info->stock_status) && $info->stock_status == $item) || (!isset($info->stock_status) && $item == 'published') ) selected @endif >{{ ucwords( str_replace('_', ' ', $item ) ) }}</option>    
            @endforeach
        @endif
    </select>
    <div class="text-muted fs-7">Set the Stock status.</div>
</div>
