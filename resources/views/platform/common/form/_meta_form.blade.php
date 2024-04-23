<div class="card card-flush py-4">
    <div class="">
        <div class="card-title">
            <h2>Meta Options</h2>
        </div>
    </div>
    <div class=" pt-0">
        <div class="mb-10">
            <label class="form-label">Meta Tag Title</label>
            <input type="text" class="form-control mb-2" name="meta_title" placeholder="Meta tag name"  value="{{ $info->meta->meta_title ?? '' }}"/>
            <div class="text-muted fs-7">Set a meta tag title. Recommended to be simple and precise keywords.</div>
        </div>
        <div class="mb-10">
            <label class="form-label">Meta Tag Description</label>
            <div id="kt_ecommerce_add_meta_description" name="kt_ecommerce_add_meta_description" class="min-h-100px mb-2">{!! $info->meta->meta_description ?? '' !!}</div>
            <textarea name="meta_description" id="meta_description" style="display: none;" cols="30" rows="10"></textarea>
            <div class="text-muted fs-7">Set a meta tag description to the product for increased SEO ranking.</div>
        </div>
        <div>
            <label class="form-label">Meta Tag Keywords</label>
            <input id="kt_ecommerce_add_product_meta_keywords" name="meta_keywords" class="form-control mb-2" value="{{ $info->meta->meta_keyword ?? '' }}" />
            <div class="text-muted fs-7">Set a list of keywords that the product is related to. Separate the keywords by adding a comma 
            <code>,</code>between each keyword.</div>
        </div>
    </div>
</div>
<script> 
    var eleme = '#kt_ecommerce_add_meta_description';
    var quill_meta = document.querySelector('#kt_ecommerce_add_meta_description');
    quill_meta = new Quill(eleme, {
                modules: {
                    toolbar: [
                        [{
                            header: [1, 2, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        ['image', 'code-block']
                    ]
                },
                placeholder: 'Type your text here...',
                theme: 'snow' // or 'bubble'
            });
    quill_meta.on('text-change', function(delta, oldDelta, source) {
        $('#meta_description').val(quill_meta.container.firstChild.innerHTML);
    });
</script>