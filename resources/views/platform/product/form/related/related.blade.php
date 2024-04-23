
<div class="d-flex flex-column gap-7 gap-lg-10">
    <div class="card card-flush py-4">
        <div class="card-body pt-2">
            <div class="mb-10 fv-row">
               <div class="col-md-6" style="width:400px">
                <label class="form-label d-block">Related Products(Minimum 5) </label>

                <select name="related_product[]" id="related_product" aria-label="Select a Category" multiple="multiple" data-placeholder="Select a Category..." class="form-select mb-2">
                    <option value=""></option>
                    @isset($otherProducts)
                        @foreach ($otherProducts as $item)
                        <option value="{{ $item->id }}"  @if( isset($info->productRelated) && in_array( $item->id, array_column( $info->productRelated->toArray(), 'to_product_id'))  ) selected="selected" @endif>
                            {{-- <option value="{{ $item->id }}" > --}}
                                {{ $item->product_name }}
                            </option>
                        @endforeach
                    @endisset
                </select>
               </div>
            </div>

            <div class="mb-10 fv-row">
                <div class="col-md-6" style="width:400px">
                <label class="form-label d-block"> Frequently Purchased Products </label>
                <select name="cross_selling_product[]" id="cross_selling_product" aria-label="Select a Category" multiple="multiple" data-placeholder="Select a Category..." class="form-select mb-2">
                    <option value=""></option>
                    @isset($otherProducts)
                        @foreach ($otherProducts as $item)
                            <option value="{{ $item->id }}"  @if( isset($info->productCrossSale) && in_array( $item->id, array_column( $info->productCrossSale->toArray(), 'to_product_id'))  ) selected="selected" @endif>
                                {{ $item->product_name }}
                            </option>
                        @endforeach
                    @endisset
                </select>     
                </div>           
            </div>
        </div>
    </div>  
</div>

<style>
  /*  .select2-container .select2-selection {
    height: 50px;
  
    overflow: scroll;
} */
    </style>