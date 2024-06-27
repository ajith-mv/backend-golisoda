<?php

use App\Http\Controllers\RazorpayPaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// Route::get('/test-invoice/{id?}', [App\Http\Controllers\TestController::class, 'invoiceSample']);
// Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/upload-image', [App\Http\Controllers\ImageUploadController::class, 'index']);
Route::get('/upload-gallery-image', [App\Http\Controllers\ImageUploadController::class, 'GalleryImage']);
Route::post('/upload-gallery-image', [App\Http\Controllers\ImageUploadController::class, 'GalleryImage'])->name('upload.bulk.gallery_image');
Route::post('/upload/image', [App\Http\Controllers\ImageUploadController::class, 'index'])->name('upload.bulk.image');
Route::get('/test', [App\Http\Controllers\TestController::class, 'testPaymentStatusTracker']);
Route::get('/test-mail-content', [App\Http\Controllers\TestController::class, 'testMailcontent']);
Route::get('/site-map', [App\Http\Controllers\TestController::class, 'generateSiteMap']);
Route::get('/test-desc', [App\Http\Controllers\TestController::class, 'testDescription']);
Route::get('/test-mail', [App\Http\Controllers\TestController::class, 'sendMail']);
Route::get('/test-payment', [App\Http\Controllers\TestController::class, 'payment']);
Route::post('/razor/payment', [App\Http\Controllers\TestController::class, 'verifySignature'])->name('razorpay.payment');
Route::get('/ccpayment', [App\Http\Controllers\Payment\CCavenueController::class, 'index'])->name('ccpayment');
Route::post('/ccpayment/request', [App\Http\Controllers\Payment\CCavenueController::class, 'ccavRequestHandler'])->name('ccavenue.request');
Route::any('/ccpayment/response', [App\Http\Controllers\Payment\CCavenueController::class, 'ccavResponseHandler'])->name('ccavenue.response');


Auth::routes();
Route::middleware(['auth'])->group(function(){
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('/dynamicview', [App\Http\Controllers\HomeController::class, 'dynamicView'])->name('home.view');
    
    Route::prefix('global')->group(function(){
        Route::get('/', [App\Http\Controllers\GlobalSettingController::class, 'index'])->name('global')->middleware(['checkAccess:visible']);
        Route::post('/save', [App\Http\Controllers\GlobalSettingController::class, 'saveForm'])->name('global.save')->middleware(['checkAccess:editable']);
        Route::post('/save/link', [App\Http\Controllers\GlobalSettingController::class, 'saveLinkForm'])->name('global.link.save')->middleware(['checkAccess:editable']);
        Route::post('/getTab', [App\Http\Controllers\GlobalSettingController::class, 'getTab'])->name('global.tab');
    });       

    Route::prefix('my-profile')->group(function(){
        Route::get('/', [App\Http\Controllers\MyProfileController::class, 'index'])->name('my-profile')->middleware(['checkAccess:visible']);
        Route::get('/password', [App\Http\Controllers\MyProfileController::class, 'getPasswordTab'])->name('my-profile.password')->middleware(['checkAccess:editable']);
        Route::post('/getTab', [App\Http\Controllers\MyProfileController::class, 'getTab'])->name('my-profile.get.tab');
        Route::post('/save', [App\Http\Controllers\MyProfileController::class, 'saveForm'])->name('my-profile.save')->middleware(['checkAccess:editable']);
    });

    Route::post('/do_upload/pincode', [App\Http\Controllers\Master\PincodeController::class, 'doBulkUpload'])->name('pincode.do-bulk.upload'); 
 
    $categoriesArray = array('sub_category', 'product-tags', 'product-labels');
    foreach ($categoriesArray as $catUrl ) {
        Route::prefix($catUrl)->group(function() use($catUrl) {
            Route::get('/', [App\Http\Controllers\Category\SubCategoryController::class, 'index'])->name($catUrl)->middleware(['checkAccess:visible']);
            Route::post('/addOrEdit', [App\Http\Controllers\Category\SubCategoryController::class, 'modalAddEdit'])->name($catUrl.'.add.edit')->middleware(['checkAccess:editable']);
            Route::post('/status', [App\Http\Controllers\Category\SubCategoryController::class, 'changeStatus'])->name($catUrl.'.status')->middleware(['checkAccess:status']);
            Route::post('/delete', [App\Http\Controllers\Category\SubCategoryController::class, 'delete'])->name($catUrl.'.delete')->middleware(['checkAccess:delete']);
            Route::post('/save', [App\Http\Controllers\Category\SubCategoryController::class, 'saveForm'])->name($catUrl.'.save');
            Route::post('/export/excel', [App\Http\Controllers\Category\SubCategoryController::class, 'export'])->name($catUrl.'.export.excel')->middleware(['checkAccess:export']);
            Route::get('/export/pdf', [App\Http\Controllers\Category\SubCategoryController::class, 'exportPdf'])->name($catUrl.'.export.pdf')->middleware(['checkAccess:export']);
        });
    }
    /***** loop for same routes */
    $routeArray = array(
        'brands' => App\Http\Controllers\Master\BrandController::class,
        'addons' => App\Http\Controllers\AddonsController::class,
        'product-category' => App\Http\Controllers\Product\ProductCategoryController::class,
        'tax' => App\Http\Controllers\Settings\TaxController::class,
        'charges' => App\Http\Controllers\ChargesController::class,
        'coupon' => App\Http\Controllers\Offers\CouponController::class,
        'discount' => App\Http\Controllers\Offers\DiscountController::class,
        'email-template' => App\Http\Controllers\Master\EmailTemplateController::class,
        'video-booking' => App\Http\Controllers\VideoBookingController::class,
        'walkthroughs' => App\Http\Controllers\WalkThroughController::class,
        'testimonials' => App\Http\Controllers\TestimonialsController::class,
        'main_category' => App\Http\Controllers\Category\MainCategoryController::class,        
        'pincode' => App\Http\Controllers\Master\PincodeController::class,
        'city' => App\Http\Controllers\Master\CityController::class,
        'state' => App\Http\Controllers\Master\StateController::class,
        'country' => App\Http\Controllers\Master\CountryController::class,
        'variation' => App\Http\Controllers\Master\VariationController::class,
        'variation-group' => App\Http\Controllers\Master\VariationGroupController::class,
        'order-status' => App\Http\Controllers\Master\OrderStatusController::class,
        'users' => App\Http\Controllers\UserController::class,
        'sms-template' => App\Http\Controllers\SmsTemplateController::class,
        'payment-gateway' => App\Http\Controllers\PaymentGatewayController::class,
        'roles' => App\Http\Controllers\Settings\RoleController::class,
        'customer' => App\Http\Controllers\CustomerController::class,
        'banner' => App\Http\Controllers\BannerController::class,
        'newsletter' => App\Http\Controllers\NewsletterController::class,
        'service-center' => App\Http\Controllers\ServiceCenterController::class,
        'service-offer' => App\Http\Controllers\ServiceOfferController::class,
        'store-locator' => App\Http\Controllers\StoreLocatorController::class,
        'store-offer' => App\Http\Controllers\StoreLocatorOfferController::class,
        'combo' => App\Http\Controllers\Product\ComboProductController::class,
        'quick-link'    => App\Http\Controllers\Master\QuickLinkController::class,
        'product-addon'    => App\Http\Controllers\ProductAddonController::class,
        'homepage-setting-field'    => App\Http\Controllers\HomePageSetting\HomepageSettingFieldController::class,
        'homepage-setting'    => App\Http\Controllers\HomePageSetting\HomepageSettingController::class,
        'warranty'    => App\Http\Controllers\WarrantyController::class,
        'manifest-generation' => App\Http\Controllers\Master\ManifestGenerationController::class,
    );
   
    foreach ($routeArray as $key => $value) {
        Route::prefix($key)->group(function() use($key, $value) {
            Route::get('/', [$value, 'index'])->name($key)->middleware(['checkAccess:visible']);
            Route::post('/addOrEdit', [$value, 'modalAddEdit'])->name($key.'.add.edit')->middleware(['checkAccess:editable']);
            Route::post('/status', [$value, 'changeStatus'])->name($key.'.status')->middleware(['checkAccess:status']);
            Route::post('/delete', [$value, 'delete'])->name($key.'.delete')->middleware(['checkAccess:delete']);
            Route::post('/save', [$value, 'saveForm'])->name($key.'.save');
            Route::post('/export/excel', [$value, 'export'])->name($key.'.export.excel')->middleware(['checkAccess:export']);
            Route::get('/export/pdf', [$value, 'exportPdf'])->name($key.'.export.pdf')->middleware(['checkAccess:export']);
        });
    }
    Route::prefix('customer')->group(function(){
        Route::get('/customer-wishlist', [App\Http\Controllers\CustomerController::class, 'wishlist'])->name('customer-wishlist'); 
        Route::get('/customer/order', [App\Http\Controllers\CustomerController::class, 'orderList'])->name('customer.order'); 
    });    

    Route::prefix('wishlist')->group(function() {
        Route::get('/', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist')->middleware(['checkAccess:editable']);
        Route::post('/addOrEdit', [App\Http\Controllers\WishlistController::class, 'modalAddEdit'])->name('wishlist.add.edit')->middleware(['checkAccess:editable']);
        Route::post('/status', [App\Http\Controllers\WishlistController::class, 'changeStatus'])->name('wishlist.status')->middleware(['checkAccess:status']);
        Route::post('/delete', [App\Http\Controllers\WishlistController::class, 'delete'])->name('wishlist.delete')->middleware(['checkAccess:delete']);
        Route::get('/view/{id}', [App\Http\Controllers\WishlistController::class, 'view'])->name('wishlist.view')->middleware(['checkAccess:visible']);
        Route::post('/save', [App\Http\Controllers\WishlistController::class, 'saveForm'])->name('wishlist.save');
        Route::post('/export/excel', [App\Http\Controllers\WishlistController::class, 'export'])->name('wishlist.export.excel')->middleware(['checkAccess:export']);
        Route::get('/export/pdf', [App\Http\Controllers\WishlistController::class, 'exportPdf'])->name('wishlist.export.pdf')->middleware(['checkAccess:export']);
    
    });

    Route::prefix('coupon')->group(function(){
        Route::get('/coupon-gendrate', [App\Http\Controllers\Offers\CouponController::class, 'couponGendrate'])->name('coupon.coupon-gendrate');
        Route::post('/coupon-apply', [App\Http\Controllers\Offers\CouponController::class, 'couponType'])->name('coupon.coupon-apply'); 
    });
    Route::post('discount/get/discount-type/data', [App\Http\Controllers\Offers\DiscountController::class, 'getDiscountTypeData'])->name('discount.coupon-apply'); 

    Route::post('/upload/pincode', [App\Http\Controllers\ChargesController::class, 'doBulkUpload'])->name('pincode.bulk.upload')->middleware(['checkAccess:editable']); 

    Route::prefix('products')->group(function(){
        Route::get('/', [App\Http\Controllers\Product\ProductController::class, 'index'])->name('products')->middleware(['checkAccess:visible']); 
        Route::get('/upload', [App\Http\Controllers\Product\ProductController::class, 'bulkUpload'])->name('products.upload')->middleware(['checkAccess:editable']); 
        Route::post('/related/upload/product', [App\Http\Controllers\Product\ProductController::class, 'doRelatedBulkUpload'])->name('related.products.bulk.upload')->middleware(['checkAccess:editable']);
        Route::post('/upload/product', [App\Http\Controllers\Product\ProductController::class, 'doBulkUpload'])->name('products.bulk.upload')->middleware(['checkAccess:editable']); 
        Route::post('/stock/upload/product', [App\Http\Controllers\Product\ProductController::class, 'doStockUpdate'])->name('products.stock.upload')->middleware(['checkAccess:editable']); 
        Route::post('/upload/attribute', [App\Http\Controllers\Product\ProductController::class, 'doAttributesBulkUpload'])->name('products.attribute.upload')->middleware(['checkAccess:editable']); 
        Route::get('/add/{id?}', [App\Http\Controllers\Product\ProductController::class, 'addEditPage'])->name('products.add.edit')->middleware(['checkAccess:editable']); 
        Route::post('/status', [App\Http\Controllers\Product\ProductController::class, 'changeStatus'])->name('products.status')->middleware(['checkAccess:status']);
        Route::post('/delete', [App\Http\Controllers\Product\ProductController::class, 'delete'])->name('products.delete')->middleware(['checkAccess:delete']);
        Route::post('/save', [App\Http\Controllers\Product\ProductController::class, 'saveForm'])->name('products.save');
        Route::post('/get/base/mrp', [App\Http\Controllers\Product\ProductController::class, 'getBaseMrpPrice'])->name('get.product.base_mrp_prce');
        Route::post('/remove/image', [App\Http\Controllers\Product\ProductController::class, 'removeImage'])->name('products.remove.image');
        Route::post('/change/image/order', [App\Http\Controllers\Product\ProductController::class, 'changeImageOrder'])->name('products.image.order');

        Route::post('/url_delete', [App\Http\Controllers\Product\ProductController::class, 'urlDelete'])->name('products_url.delete')->middleware(['checkAccess:delete']);
        
        Route::post('/upload/gallery', [App\Http\Controllers\Product\ProductController::class, 'uploadGallery'])->name('products.upload.gallery');
        Route::get('/product_attriut_set_export', [App\Http\Controllers\Product\ProductController::class, 'exportAttriuteSet'])->name('product_attriut_set_export');
        Route::get('/related_product_export', [App\Http\Controllers\Product\ProductController::class, 'exportAttriuteSetRelatedProduct'])->name('related_product_set_export');
        Route::get('/export/excel', [App\Http\Controllers\Product\ProductController::class, 'export'])->name('products.export.excel')->middleware(['checkAccess:export']);
        Route::get('/export/pdf', [App\Http\Controllers\Product\ProductController::class, 'exportPdf'])->name('products.export.pdf')->middleware(['checkAccess:export']);

        Route::post('/variation/row', [App\Http\Controllers\Product\ProductVariationController::class, 'getAttributeRow'])->name('products.variation.row'); 
        Route::get('/variation/value', [App\Http\Controllers\Product\ProductVariationController::class, 'getvariationvalue'])->name('products.variation.value'); 

        /***** Attribute set values */
        Route::post('/attribute/row', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'getAttributeRow'])->name('products.attribute.row'); 

        /***** Attribute set values */
        Route::get('/attribute', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'index'])->name('product-attribute')->middleware(['checkAccess:visible']); 
        Route::post('/attribute/addOrEdit', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'modalAddEdit'])->name('product-attribute.add.edit')->middleware(['checkAccess:editable']);
        Route::post('/attribute/status', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'changeStatus'])->name('product-attribute.status')->middleware(['checkAccess:status']);
        Route::post('/attribute/delete', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'delete'])->name('product-attribute.delete')->middleware(['checkAccess:delete']);
        Route::post('/attribute/save', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'saveForm'])->name('product-attribute.save')->middleware(['checkAccess:editable']);
        Route::post('/attribute/export/excel', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'export'])->name('product-attribute.export.excel')->middleware(['checkAccess:export']);
        Route::get('/attribute/export/pdf', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'exportPdf'])->name('product-attribute.export.pdf')->middleware(['checkAccess:export']);
        /****** Product Collection */
        Route::get('/collection', [App\Http\Controllers\Product\ProductCollectionController::class, 'index'])->name('product-collection')->middleware(['checkAccess:visible']); 
        Route::post('/collection/addOrEdit', [App\Http\Controllers\Product\ProductCollectionController::class, 'modalAddEdit'])->name('product-collection.add.edit')->middleware(['checkAccess:editable']);
        Route::post('/collection/status', [App\Http\Controllers\Product\ProductCollectionController::class, 'changeStatus'])->name('product-collection.status')->middleware(['checkAccess:status']);
        Route::post('/collection/delete', [App\Http\Controllers\Product\ProductCollectionController::class, 'delete'])->name('product-collection.delete')->middleware(['checkAccess:delete']);
        Route::post('/collection/save', [App\Http\Controllers\Product\ProductCollectionController::class, 'saveForm'])->name('product-collection.save')->middleware(['checkAccess:editable']);
        Route::post('/collection/export/excel', [App\Http\Controllers\Product\ProductCollectionController::class, 'export'])->name('product-collection.export.excel')->middleware(['checkAccess:export']);
        Route::get('/collection/export/pdf', [App\Http\Controllers\Product\ProductCollectionController::class, 'exportPdf'])->name('product-collection.export.pdf')->middleware(['checkAccess:export']);
    });

    Route::post('/getProduct/category/list', [App\Http\Controllers\CommonController::class, 'getProductCategoryList'])->name('common.category.dropdown');
    Route::post('/getProduct/brand/list', [App\Http\Controllers\CommonController::class, 'getProductBrandList'])->name('common.brand.dropdown');
    Route::post('/getProduct/warranty/list', [App\Http\Controllers\CommonController::class, 'getProductWarrantyList'])->name('common.warranty.dropdown');
    Route::post('/getProduct/dynamic/list', [App\Http\Controllers\CommonController::class, 'getProductDynamicList'])->name('common.dynamic.dropdown');

    Route::prefix('customer')->group(function(){
        Route::get('/coupon-gendrate', [App\Http\Controllers\CustomerController::class, 'couponGendrate'])->name('customer.coupon-gendrate');
        Route::post('/coupon-apply', [App\Http\Controllers\CustomerController::class, 'couponType'])->name('customer.coupon-apply');
        Route::post('/customer/delete', [App\Http\Controllers\CustomerController::class, 'customerDelete'])->name('customer.delete')->middleware(['checkAccess:delete']);
        Route::get('/customer/view/{id}', [App\Http\Controllers\CustomerController::class, 'view'])->name('customer.view')->middleware(['checkAccess:visible']);
        Route::get('/add-address', [App\Http\Controllers\CustomerController::class, 'addAddress'])->name('customer.add-address')->middleware(['checkAccess:editable']);
        Route::post('/address', [App\Http\Controllers\CustomerController::class, 'customerAddress'])->name('customer.address');
        Route::post('/address/list', [App\Http\Controllers\CustomerController::class, 'addressList'])->name('customer.address.list')->middleware(['checkAccess:visible']);
        Route::post('/address/delete', [App\Http\Controllers\CustomerController::class, 'addressDelete'])->name('customer.address.delete')->middleware(['checkAccess:delete']);
    });

    Route::prefix('order')->group(function(){
        Route::get('/', [App\Http\Controllers\OrderController::class, 'index'])->name('order');
        Route::post('/view', [App\Http\Controllers\OrderController::class, 'orderView'])->name('order.view');
        Route::post('/open/orderStatus/modal', [App\Http\Controllers\OrderController::class, 'openOrderStatusModal'])->name('order.status.modal');
        Route::get('order-count', [App\Http\Controllers\OrderController::class, 'orderCountGolbal'])->name('order-count');
        Route::post('/change/order/status', [App\Http\Controllers\OrderController::class, 'changeOrderStatus'])->name('order.change.status');
        Route::post('/export/excel', [App\Http\Controllers\OrderController::class, 'export'])->name('order.export.excel')->middleware(['checkAccess:export']);
    });

    Route::prefix('reports')->middleware(['checkAccess:visible'])->group(function(){
        Route::get('/sale', [App\Http\Controllers\ReportProductController::class, 'index'])->name('reports.sale');
        Route::post('/excel/export', [App\Http\Controllers\ReportProductController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/productwisesale', [App\Http\Controllers\ProductwiseSaleReportController::class, 'index'])->name('reports.productwise');
        Route::post('/excel/export', [App\Http\Controllers\ProductwiseSaleReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/customerwisesale', [App\Http\Controllers\CustomerwiseSaleReportController::class, 'index'])->name('reports.customerwise');
        Route::post('/excel/export', [App\Http\Controllers\CustomerwiseSaleReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('payment-report', [App\Http\Controllers\PaymentReportController::class, 'index'])->name('reports.payment');
        Route::post('/payment-view', [App\Http\Controllers\PaymentReportController::class, 'paymentView'])->name('payment-reports.view');
        Route::post('/export/excel', [App\Http\Controllers\PaymentReportController::class, 'export'])->name('payment-reports.export.excel');
        Route::get('/vendor-wise-sale', [App\Http\Controllers\VendorWiseSaleReportController::class, 'index'])->name('reports.vendor.wise');
        Route::post('/vendor-wise-sale/view', [App\Http\Controllers\VendorWiseSaleReportController::class, 'viewInvoice'])->name('vendor_wise_sale.view');
        Route::get('/vendor-wise-sale/download', [App\Http\Controllers\VendorWiseSaleReportController::class, 'downloadInvoice'])->name('vendor_wise_sale.download');
        Route::post('/vendor-wise-sale/send-invoice', [App\Http\Controllers\VendorWiseSaleReportController::class, 'sendInvoice'])->name('vendor_wise_sale.send');


    });
    
    Route::prefix('payment')->group(function(){
        Route::get('/', [App\Http\Controllers\PaymentController::class, 'index'])->name('payment');
        Route::post('/view', [App\Http\Controllers\PaymentController::class, 'paymentView'])->name('payment.view');
        Route::post('/export/excel', [App\Http\Controllers\PaymentController::class, 'export'])->name('payment.export.excel')->middleware(['checkAccess:export']);
    });

    Route::prefix('review')->group(function(){
        Route::get('/', [App\Http\Controllers\ReviewController::class, 'index'])->name('review');
        Route::post('/view', [App\Http\Controllers\ReviewController::class, 'paymentView'])->name('review.view');
        Route::post('/status', [App\Http\Controllers\ReviewController::class, 'changeStatus'])->name('review.status')->middleware(['checkAccess:status']);
        Route::post('/delete', [App\Http\Controllers\ReviewController::class, 'delete'])->name('review.delete')->middleware(['checkAccess:delete']);
        Route::post('/export/excel', [App\Http\Controllers\ReviewController::class, 'export'])->name('review.export.excel')->middleware(['checkAccess:export']);
    
    });
    ## contact enquiry 
    Route::get('/enquiry', [App\Http\Controllers\EnquiryController::class, 'index'])->name('enquiry');
    Route::post('/export/enquiry', [App\Http\Controllers\EnquiryController::class, 'export'])->name('enquiry.export.excel');


   
});

Route::get('razorpay-payment', [RazorpayPaymentController::class, 'index']);
Route::post('razorpay/process', [RazorpayPaymentController::class, 'razorpay_response'])->name('razorpay.payment.store');
Route::any('/payment/failed', [RazorpayPaymentController::class, 'fail_page'])->name('fail.page');
