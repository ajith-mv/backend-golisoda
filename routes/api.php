<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/upload-gallery-image', [App\Http\Controllers\ImageUploadController::class, 'GalleryImage']);

Route::get('/test-invoice/{id?}', [App\Http\Controllers\TestController::class, 'invoiceSample']);
Route::get('/test', [App\Http\Controllers\TestController::class, 'index']);
Route::post('news-letter', [App\Http\Controllers\NewsletterController::class, 'siteNewsletter']);
Route::get('/get/site/info', [App\Http\Controllers\Api\SiteController::class, 'siteInfo']);
Route::get('/get/home/details', [App\Http\Controllers\Api\CommonController::class, 'getAllHomeDetails']);
Route::get('/get/home/service/places', [App\Http\Controllers\Api\CommonController::class, 'getHomeServicePlaces']);
Route::get('/get/topMenu/{slug?}', [App\Http\Controllers\Api\MenuController::class, 'getTopMenu']);
Route::get('/get/allMenu', [App\Http\Controllers\Api\MenuController::class, 'getAllMenu']);
Route::get('/get/testimonials', [App\Http\Controllers\Api\CommonController::class, 'getAllTestimonials']);
Route::get('/get/history', [App\Http\Controllers\Api\CommonController::class, 'getAllHistoryVideo']);
Route::get('/get/banners', [App\Http\Controllers\Api\CommonController::class, 'getAllBanners']);
Route::get('/get/brands', [App\Http\Controllers\Api\CommonController::class, 'getAllBrands']);
Route::get('/get/brands/top', [App\Http\Controllers\Api\CommonController::class, 'getTopBrands']);
Route::get('/get/brands/all/{slug}', [App\Http\Controllers\Api\CommonController::class, 'getBrandInfo']);
Route::get('/get/brands/alphabets', [App\Http\Controllers\Api\CommonController::class, 'getBrandByAlphabets']);

Route::post('/get/discount/collections', [App\Http\Controllers\Api\CommonController::class, 'getDiscountCollections']);
Route::post('/get/handpicked/collections', [App\Http\Controllers\Api\CommonController::class, 'getHandPickedCollections']);
Route::post('/get/subcategory/collections', [App\Http\Controllers\Api\CommonController::class, 'getSubCategoryCollections']);
Route::post('/get/model/data', [App\Http\Controllers\Api\CommonController::class, 'getProcessorModelData']);

Route::get('/get/product/collections/{order_by?}', [App\Http\Controllers\Api\CollectionController::class, 'getProductCollections']);
Route::get('/get/product/collections/byorder/{order_by}', [App\Http\Controllers\Api\CollectionController::class, 'getProductCollectionByOrder']);
Route::get('/browseHome', [App\Http\Controllers\Api\BrowseController::class, 'index']);
Route::get('/get/exclusive', [App\Http\Controllers\Api\FilterController::class, 'exclusiveProduct']);
Route::post('/compare/product', [App\Http\Controllers\Api\CompareController::class, 'index']);
Route::post('/submit/contact/form', [App\Http\Controllers\Api\CommonController::class, 'submitContactForm']);

Route::post('/get/filter/sidemenus', [App\Http\Controllers\Api\FilterController::class, 'getFilterStaticSideMenu']);
Route::post('/get/product/list/category', [App\Http\Controllers\Api\FilterController::class, 'getProductListCategory']);

Route::get('/get/products', [App\Http\Controllers\Api\FilterController::class, 'getProducts']);
Route::post('/get/products/by/slug', [App\Http\Controllers\Api\FilterController::class, 'getProductBySlug']);
Route::get('/get/states', [App\Http\Controllers\Api\CommonController::class, 'getSates']);
Route::post('/get/meta', [App\Http\Controllers\Api\CommonController::class, 'getMetaInfo']);
Route::post('/get/global/search', [App\Http\Controllers\Api\FilterController::class, 'globalSearch']);
Route::post('/get/other/category', [App\Http\Controllers\Api\FilterController::class, 'getOtherCategories']);

Route::post('/register/customer', [App\Http\Controllers\Api\CustomerController::class, 'registerCustomer']);
Route::post('/login', [App\Http\Controllers\Api\CustomerController::class, 'doLogin']);
Route::post('/send/password/link', [App\Http\Controllers\Api\CustomerController::class, 'sendPasswordLink']);
Route::post('/reset/password', [App\Http\Controllers\Api\CustomerController::class, 'resetPasswordLink']);
Route::post('/check/tokenValid', [App\Http\Controllers\Api\CustomerController::class, 'checkValidToken']);
Route::post('/verify/account', [App\Http\Controllers\Api\CustomerController::class, 'verifyAccount']);

Route::post('/serviceCenter', [App\Http\Controllers\Api\ServiceController::class, 'getServiceCenter']);
Route::post('/serviceCenterDetail', [App\Http\Controllers\Api\ServiceController::class, 'getServiceCenterDetail']);

Route::post('/store-locator', [App\Http\Controllers\Api\StoreLocatorController::class, 'getStoreLocator']);
Route::post('/store-service', [App\Http\Controllers\Api\StoreLocatorController::class, 'getStoreOrService']);
Route::post('/storeLocatorDetail', [App\Http\Controllers\Api\StoreLocatorController::class, 'getStoreLocatorDetail']);
Route::get('/quickLink', [App\Http\Controllers\Api\QuickLinkController::class, 'index']);

Route::post('/check/product/availability', [App\Http\Controllers\Api\ProductAvailCheckController::class, 'index']);

Route::post('/add/cart', [App\Http\Controllers\Api\CartController::class, 'addToCart']);
Route::post('/get/cart', [App\Http\Controllers\Api\CartController::class, 'getCarts']);
Route::post('/update/cart', [App\Http\Controllers\Api\CartController::class, 'updateCart']);
Route::post('/delete/cart', [App\Http\Controllers\Api\CartController::class, 'deleteCart']);    
Route::post('/clear/cart', [App\Http\Controllers\Api\CartController::class, 'clearCart']); 
Route::post('/delete/addon', [App\Http\Controllers\Api\CartController::class, 'deleteAddonItems']); 

Route::post('/list/customer/reviews', [App\Http\Controllers\Api\CustomerReviewController::class, 'listReviews']);

Route::get('/ccavenue/payment/processing/{customer_id?}/{order_id?}', [App\Http\Controllers\Payment\CCavenueController::class, 'startPayment'])->name('ccav.payment.start');

Route::get('/site-map', [App\Http\Controllers\TestController::class, 'generateSiteMap']);

Route::middleware(['client'])->group(function(){
    //get profile data
    Route::post('/get/profile', [App\Http\Controllers\Api\CustomerController::class, 'getProfileDetails']);
    Route::post('/update/profile', [App\Http\Controllers\Api\CustomerController::class, 'updateProfile']);
    Route::post('/change/password', [App\Http\Controllers\Api\CustomerController::class, 'changePassword']);
    /**
     * Customer Addresses
     */
    Route::post('/get/addresses', [App\Http\Controllers\Api\CustomerController::class, 'getCustomerAddressDetails']);
    Route::post('/save/customer/address', [App\Http\Controllers\Api\CustomerController::class, 'addUpdateCustomerAddress']);
    Route::post('/set/default/address', [App\Http\Controllers\Api\CustomerController::class, 'setDefaultAddress']);
    Route::post('/delete/customer/address', [App\Http\Controllers\Api\CustomerController::class, 'deleteCustomerAddress']);

    /**
     * Customer Reviews
     */
    Route::post('/add/customer/reviews', [App\Http\Controllers\Api\CustomerReviewController::class, 'addReviews']);
    /**
     *  Add or Remove Whishlist
     */
    Route::post('/add/remove/whishlist', [App\Http\Controllers\Api\CustomerController::class, 'setWishlists']);
    Route::post('/clear/whishlist', [App\Http\Controllers\Api\CustomerController::class, 'clearWishlist']);
    Route::post('/get/whishlist', [App\Http\Controllers\Api\CustomerController::class, 'getWishlist']);
    
    Route::post('/get/shipping/charges', [App\Http\Controllers\Api\CartController::class, 'getShippingCharges']);
    Route::post('/set/shipping/charges', [App\Http\Controllers\Api\Couponcontroller::class, 'setShippingCharges']);
    Route::post('/calculation/cod', [App\Http\Controllers\Api\CartController::class, 'calculationCod']);
    Route::post('/proceed/cod', [App\Http\Controllers\Api\CheckoutController::class, 'proceedCod']);
    Route::post('/proceed/checkout', [App\Http\Controllers\Api\CheckoutController::class, 'proceedCheckout']);
    Route::post('/verify/payment/signature', [App\Http\Controllers\Api\CheckoutController::class, 'verifySignature']);

    /**
     * ccave routes
     */
    Route::post('/proceed/ccav/checkout', [App\Http\Controllers\Payment\CCavenueController::class, 'proceedCheckout']);
    Route::post('/verify/ccav/token', [App\Http\Controllers\Payment\CCavenueController::class, 'verifyCCavenueTransaction']);
    
    Route::post('/set/recent', [App\Http\Controllers\Api\CommonController::class, 'setRecentView']);
    
    Route::post('/apply/coupon', [App\Http\Controllers\Api\Couponcontroller::class, 'applyCoupon']);    
    Route::post('/remove/coupon', [App\Http\Controllers\Api\Couponcontroller::class, 'removeCoupon']);    

    Route::post('/get/orders', [App\Http\Controllers\Api\OrderController::class, 'getOrders']);
    Route::post('/get/orders-status/{customer_id}', [App\Http\Controllers\Api\OrderController::class, 'getOrderStatus']);

    
    Route::post('/get/orderByno', [App\Http\Controllers\Api\OrderController::class, 'getOrderByOrderNo']);
    
    Route::post('/cancel/request/orders', [App\Http\Controllers\Api\OrderController::class, 'requestCancelOrder']);

    Route::post('/get/recent/view', [App\Http\Controllers\Api\CollectionController::class, 'getRecentViews']);
 

});

