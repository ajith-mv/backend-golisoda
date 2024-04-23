<select name="label_id" id="label_id" aria-label="Select a Brand" data-control="select2" data-placeholder="Select a Label..." class="form-select mb-2">
    <option value=""></option>
    @isset($productLabels->subCategory)
        @foreach ($productLabels->subCategory as $item)
            <option value="{{ $item->id }}" 
                @if( (isset( $label_id ) && $label_id == $item->id ) || ( isset($info->label_id) && $info->label_id == $item->id ) ) selected="selected" @endif>
                {{ $item->name }} 
            </option>
        @endforeach
    @endisset
</select>
<script>
    setTimeout(() => {
        $('#label_id').select2();
    }, 200);
</script>