
<div class="card card-flush py-4">
    
    <div class="card-header">
        <div class="card-title">
            <h2>Meta Options</h2>
        </div>
    </div>
 
    <div class="card-body pt-0">
        
        <div class="mb-10">
            
            <label class="form-label">Meta Tag Title</label>
            <input type="text" class="form-control mb-2" name="meta_title" value="{{ $info->productMeta->meta_title ?? '' }}" placeholder="Meta tag name" />
            <div class="text-muted fs-7">Set a meta tag title. Recommended to be simple and precise keywords.</div>
            
        </div>
      
        <div class="mb-10">
          
            <label class="form-label">Meta Tag Description</label>
            <div id="kt_ecommerce_add_product_meta_description" name="kt_ecommerce_add_product_meta_description" class="min-h-100px mb-2">{!! $info->productMeta->meta_description ?? '' !!}</div>
            <textarea name="meta_description" id="meta_description" cols="30" rows="10" style="display: none;">{{ $info->productMeta->meta_description ?? '' }}</textarea>
            <div class="text-muted fs-7">Set a meta tag description to the product for increased SEO ranking.</div>
            
        </div>
      
        <div>
          
            <label class="form-label">Meta Tag Keywords</label>
            <input id="kt_ecommerce_add_product_meta_keywords" value="{{ $info->productMeta->meta_keyword ?? '' }}" name="meta_keywords" class="form-control mb-2" />
            <div class="text-muted fs-7">Set a list of keywords that the product is related to. Separate the keywords by adding a comma 
            <code>,</code>between each keyword.</div>
            
        </div>
        
    </div>
    
</div>
