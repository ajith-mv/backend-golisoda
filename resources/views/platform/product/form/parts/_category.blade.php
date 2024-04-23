
<div class="card-body pt-0 fv-row">
    
    <select name="category_id" id="category_id" aria-label="Select a Category" data-control="select2" data-placeholder="Select a Category..." class="form-select mb-2 required">
        <option value=""></option>
        @isset($productCategory)
            @foreach ($productCategory as $item)
                <option value="{{ $item->id }}" 
                @if( (isset( $category_id ) && $category_id == $item->id ) || ( isset($info->category_id) && $info->category_id == $item->id ) ) selected @endif>{{ $item->name }} - {{ $item->parent->name ?? 'Parent' }} </option>
            @endforeach
        @endisset
    </select>
</div>
<script>
    var cat_id = '{{ $info->category_id ?? "" }}';
    setTimeout(() => {
        $('#category_id').select2();
        if( cat_id ) {
            $('#category_id').val(cat_id);
        }
        
    }, 200);
</script>