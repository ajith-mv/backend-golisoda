
<select name="warranty_id" id="warranty_id" aria-label="Select a Warranty" data-control="select2" data-placeholder="Select a Warranty..." class="form-select mb-2">
    <option value="0"> None </option>
    @isset($warranties)
        @foreach ($warranties as $item)
            <option value="{{ $item->id }}" 
                @if( (isset( $warranty_id ) && $warranty_id == $item->id ) || ( isset($info->warranty_id) && $info->warranty_id == $item->id ) ) selected="selected" @endif>
                {{ $item->name }} </option>
        @endforeach
    @endisset
</select>

<script>
    setTimeout(() => {
        $('#warranty_id').select2();
    }, 100);
</script>