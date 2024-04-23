
<select name="tag_id" id="tag_id" aria-label="Select a Brand" data-control="select2" data-placeholder="Select a Tags..." class="form-select mb-2">
    <option value=""></option>
    @isset($productTags->subCategory)
        @foreach ($productTags->subCategory as $item)
            <option value="{{ $item->id }}" 
                @if( (isset( $tag_id ) && $tag_id == $item->id ) || ( isset($info->tag_id) && $info->tag_id == $item->id ) ) selected="selected" @endif>
                {{ $item->name }} </option>
        @endforeach
    @endisset
</select>
<script>
    setTimeout(() => {
        $('#tag_id').select2();
    }, 100);
</script>