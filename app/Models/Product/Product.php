<?php

namespace App\Models\Product;

use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\ProductAddon;
use App\Models\Settings\Tax;
use App\Models\User;
use App\Models\Warranty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product\ProductUrl;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'product_name',
        'hsn_code',
        'product_url',
        'sku',
        'strike_price',
        'discount_percentage',
        'price',
        'mrp',
        'sale_price',
        'sale_start_date',
        'sale_end_date',
        'status',
        'quantity',
        'has_video_shopping',
        'stock_status',
        'brand_id',
        'category_id',
        'tag_id',
        'label_id',
        'is_display_home',
        'is_featured',
        'is_best_selling',
        'is_new',
        'tax_id',
        'description',
        'technical_information',
        'feature_information',
        'specification',
        'brochure_upload',
        'base_image',
        'approved_by',
        'approved_at',
        'added_by',
        'warranty_id',
        'no_of_items',
        'material_ingredients',
        'features',
        'benefits'

    ];

    public function productCategory()
    {
        return $this->hasOne(ProductCategory::class, 'id', 'category_id');
    }

    public function productBrand()
    {
        return $this->hasOne(Brands::class, 'id', 'brand_id');
    }

    public function productTag()
    {
        return $this->hasOne(MainCategory::class, 'id', 'tag_id')->where(['slug' => 'product-tags', 'status' => 'published']);
    }

    public function productLabel()
    {
        return $this->hasOne(MainCategory::class, 'id', 'label_id')->where(['slug' => 'product-labels', 'status' => 'published']);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id')->orderBy('order_by', 'asc');
    }

    public function productMeasurement()
    {
        return $this->hasOne(ProductMeasurement::class, 'product_id', 'id');
    }

    public function productDiscount()
    {
        return $this->hasOne(ProductDiscount::class, 'product_id', 'id');
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductWithAttributeSet::class, 'product_id', 'id')->select('id', 'title', 'attribute_values', 'product_attribute_set_id','is_overview', 'order_by')->orderBy('order_by');
    }

    public function productOverviewAttributes()
    {
        return $this->hasMany(ProductWithAttributeSet::class, 'product_id', 'id')->where('is_overview', 'yes')->select('id', 'title', 'attribute_values', 'product_attribute_set_id','is_overview', 'order_by')->orderBy('order_by');
    }

    public function productMeta()
    {
        return $this->hasOne(ProductMetaTag::class, 'product_id', 'id')->select('id','product_id', 'meta_title', 'meta_keyword', 'meta_description');
    }

    public function userInfo()
    {
        return $this->hasOne(User::class, 'id', 'added_by');
    }

    public function productRelated()
    {
        return $this->hasMany(ProductRelatedRelation::class, 'from_product_id', 'id');
    }

    public function productCrossSale()
    {
        return $this->hasMany(ProductCrossSaleRelation::class, 'from_product_id', 'id');
    }

    public function productLinks()
    {
        return $this->hasMany(ProductLink::class, 'product_id', 'id')->where('url_type', '!=', 'video_link');
    }

    public function productAllLinks()
    {
        return $this->hasMany(ProductLink::class, 'product_id', 'id');
    }

    public function productVideoLinks()
    {
        return $this->hasMany(ProductLink::class, 'product_id', 'id')->where('url_type', 'video_link');
    }

    public function productDescription()
    {
        return $this->hasMany(ProductDescription::class, 'product_id', 'id')->orderBy('order_by', 'asc');
    }


    public function productUrl()
    {
        return $this->hasMany(ProductUrl::class, 'product_id', 'id')->orderBy('order_by', 'asc');
    }

    public function productAddons()
    {
        
        return $this->hasMany(ProductAddonProduct::class, 'product_id', 'id')
                        ->join('product_addons', 'product_addons.id', '=', 'product_addon_products.product_addon_id')
                        ->whereNull('product_addons.deleted_at')
                        ->where('product_addon_products.type', 'product')
                        ->where('product_addons.status', 'published');
                        
    }
   

    public function reviews() {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }

    public function warranty()
    {
        return $this->hasOne(Warranty::class, 'id', 'warranty_id')->select('id', 'name', 'warranty_period', 'warranty_period_type', 'description');
    }

    public function tax()
    {
        return $this->hasOne(Tax::class, 'id', 'tax_id');
    }
    public function productCategoryList()
    {
        return $this->hasOne(ProductCategory::class, 'id', 'category_id');
    }

}
