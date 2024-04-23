
<div class="card-body pt-0 fv-row">
    <select name="brand_id" id="brand_id" aria-label="Select a Brand" data-control="select2" data-placeholder="Select a Brand..." class="form-select mb-2 required">
        <option value=""></option>
        @isset($brands)
            @foreach ($brands as $item)
                <option value="{{ $item->id }}"
                    @if( (isset( $brand_id ) && $brand_id == $item->id ) || ( isset($info->brand_id) && $info->brand_id == $item->id ) )  selected="selected" @endif>
                    {{ $item->brand_name }} 
                </option>
            @endforeach
        @endisset
    </select>
</div>
<script>
    // setTimeout(() => {
    //     $('#brand_id').select2();
    // }, 200);    
</script>