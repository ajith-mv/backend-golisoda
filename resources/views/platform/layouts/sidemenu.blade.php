<div class="aside-menu flex-column-fluid">
    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
        data-kt-scroll-offset="0">
        <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
            id="#kt_aside_menu" data-kt-menu="true" data-kt-menu-expand="false">
            <div class="menu-item">
                <a class="menu-link" href="{{ url('/') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect x="2" y="2" width="9" height="9" rx="2"
                                    fill="currentColor" />
                                <rect opacity="0.3" x="13" y="2" width="9" height="9"
                                    rx="2" fill="currentColor" />
                                <rect opacity="0.3" x="13" y="13" width="9" height="9"
                                    rx="2" fill="currentColor" />
                                <rect opacity="0.3" x="2" y="13" width="9" height="9"
                                    rx="2" fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title"> Dashboard </span>
                </a>
            </div>
            @if (access()->hasAccess([
                    'product-category',                    
                    'combo',
                    'product-labels',
                    'products',
                    'product-attribute',
                    'product-collection',
                ]))
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion @if (request()->routeIs([
                            'product-category',
                            'combo',                            
                            'product-labels',
                            'products',
                            'products.*',
                            'product-attribute',
                            'product-collection',
                        ])) hover show @endif">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3"
                                        d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z"
                                        fill="currentColor" />
                                    <path opacity="0.3"
                                        d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z"
                                        fill="currentColor" />
                                    <path opacity="0.3"
                                        d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z"
                                        fill="currentColor" />
                                    <path
                                        d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Products</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @if (access()->hasAccess(['product-category']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['product-category'])) active @endif"
                                    href="{{ route('product-category') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Product Categories</span>
                                </a>
                            </div>
                        @endif                        
                        @if (access()->hasAccess(['product-labels']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['product-labels'])) active @endif"
                                    href="{{ route('product-labels') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Product Labels</span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['products']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['products', 'products.*'])) active @endif"
                                    href="{{ route('products') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Products</span>
                                </a>
                            </div>
                        @endif
                        {{-- <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs(['combo'])) active @endif" href="{{ route('combo') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Combo Product</span>
                        </a>
                    </div> --}}
                        @if (access()->hasAccess(['product-collection']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['product-collection'])) active @endif"
                                    href="{{ route('product-collection') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Product Collection</span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['product-attribute']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['product-attribute'])) active @endif"
                                    href="{{ route('product-attribute') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Product Attributes</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @if (access()->hasAccess('product-addon'))
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs(['product-addon'])) active @endif"
                        href="{{ route('product-addon') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <rect x="2" y="2" width="9" height="9"
                                        rx="2" fill="currentColor" />
                                    <rect opacity="0.3" x="13" y="2" width="9"
                                        height="9" rx="2" fill="currentColor" />
                                    <rect opacity="0.3" x="13" y="13" width="9"
                                        height="9" rx="2" fill="currentColor" />
                                    <rect opacity="0.3" x="2" y="13" width="9"
                                        height="9" rx="2" fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Addons</span>
                    </a>
                </div>
            @endif
            @if (access()->hasAccess('customer'))
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs(['customer'])) active @elseif(request()->routeIs(['customer.view'])) active @endif"
                        href="{{ route('customer') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3"
                                        d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z"
                                        fill="currentColor" />
                                    <path
                                        d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Customer</span>
                    </a>
                </div>
            @endif
            @if (access()->hasAccess('coupon'))
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs(['coupon'])) active @endif"
                        href="{{ route('coupon') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <rect x="2" y="2" width="9" height="9"
                                        rx="2" fill="currentColor" />
                                    <rect opacity="0.3" x="13" y="2" width="9"
                                        height="9" rx="2" fill="currentColor" />
                                    <rect opacity="0.3" x="13" y="13" width="9"
                                        height="9" rx="2" fill="currentColor" />
                                    <rect opacity="0.3" x="2" y="13" width="9"
                                        height="9" rx="2" fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Coupons</span>
                    </a>
                </div>
            @endif
           
            @if (access()->hasAccess(['order-status', 'order']))
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion mb-1 @if (request()->routeIs(['order-status', 'order'])) hover active show @endif">
                    <span class="menu-link ">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="bi-chat-left fs-3"></i>
                            </span>
                        </span>
                        <span class="menu-title"> Orders <span class="mx-3 badge badge-danger order_count_global"></span></span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['order'])) active @endif"
                                href="{{ route('order') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Order List </span>
                            </a>
                        </div>
                        @if (access()->hasAccess(['order-status']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['order-status'])) active @endif"
                                    href="{{ route('order-status') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title"> Order Status </span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['manifest-generation']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['manifest-generation'])) active @endif"
                                    href="{{ route('manifest-generation') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title"> Manifest Generation </span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion mb-1">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <path d="M22 7H2V11H22V7Z" fill="currentColor" />
                                <path opacity="0.3"
                                    d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19ZM14 14C14 13.4 13.6 13 13 13H5C4.4 13 4 13.4 4 14C4 14.6 4.4 15 5 15H13C13.6 15 14 14.6 14 14ZM16 15.5C16 16.3 16.7 17 17.5 17H18.5C19.3 17 20 16.3 20 15.5C20 14.7 19.3 14 18.5 14H17.5C16.7 14 16 14.7 16 15.5Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Payments</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('payment') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Payment List</span>
                        </a>
                    </div>
                </div>
            </div>
            @if (access()->hasAccess(['reports']))
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion mb-1 @if (request()->routeIs(['reports.*'])) hover show @endif">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3"
                                        d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z"
                                        fill="currentColor" />
                                    <path d="M20 8L14 2V6C14 7.10457 14.8954 8 16 8H20Z" fill="currentColor" />
                                    <path
                                        d="M10.3629 14.0084L8.92108 12.6429C8.57518 12.3153 8.03352 12.3153 7.68761 12.6429C7.31405 12.9967 7.31405 13.5915 7.68761 13.9453L10.2254 16.3488C10.6111 16.714 11.215 16.714 11.6007 16.3488L16.3124 11.8865C16.6859 11.5327 16.6859 10.9379 16.3124 10.5841C15.9665 10.2565 15.4248 10.2565 15.0789 10.5841L11.4631 14.0084C11.1546 14.3006 10.6715 14.3006 10.3629 14.0084Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Reports</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['reports.sale'])) active @endif"
                                href="{{ route('reports.sale') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Standard Sales Report</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['reports.productwise'])) active @endif"
                                href="{{ route('reports.productwise') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Product Wise Sales Report</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['reports.customerwise'])) active @endif"
                                href="{{ route('reports.customerwise') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Customer Wise Sales Report</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['reports.payment'])) active @endif"
                                href="{{ route('reports.payment') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Payment Transaction Report</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            @if (access()->hasAccess(['tax', 'charges']))
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion mb-1 @if (request()->routeIs(['tax', 'charges'])) hover show @endif">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="bi-chat-left fs-3"></i>
                            </span>
                        </span>
                        <span class="menu-title"> Taxes & Charges </span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['tax'])) active @endif"
                                href="{{ route('tax') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Tax</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['charges'])) active @endif"
                                href="{{ route('charges') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Shipping Charges</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            @if (access()->hasAccess([ 'store-locator','store-offer']))
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion mb-1 @if (request()->routeIs(['store-locator','store-offer'])) hover show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <i class="bi-chat-left fs-3"></i>
                        </span>
                    </span>
                    <span class="menu-title"> Store Locators </span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs(['store-locator'])) active @endif"
                            href="{{ route('store-locator') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Stores</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs(['store-offer'])) active @endif"
                            href="{{ route('store-offer') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Store Locator Offer</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif
            <!--@if (access()->hasAccess(['service-center','service-offer']))-->
            <!--<div data-kt-menu-trigger="click" class="menu-item menu-accordion mb-1 @if (request()->routeIs([ 'service-center','service-offer'])) hover show @endif">-->
            <!--    <span class="menu-link">-->
            <!--        <span class="menu-icon">-->
            <!--            <span class="svg-icon svg-icon-2">-->
            <!--                <i class="bi-chat-left fs-3"></i>-->
            <!--            </span>-->
            <!--        </span>-->
            <!--        <span class="menu-title"> Service Centers </span>-->
            <!--        <span class="menu-arrow"></span>-->
            <!--    </span>-->
            <!--    <div class="menu-sub menu-sub-accordion">-->
            <!--        <div class="menu-item">-->
            <!--            <a class="menu-link @if (request()->routeIs(['service-center'])) active @endif"-->
            <!--                href="{{ route('service-center') }}">-->
            <!--                <span class="menu-bullet">-->
            <!--                    <span class="bullet bullet-dot"></span>-->
            <!--                </span>-->
            <!--                <span class="menu-title">Services Center</span>-->
            <!--            </a>-->
            <!--        </div>-->
            <!--        <div class="menu-item">-->
            <!--            <a class="menu-link @if (request()->routeIs(['service-offer'])) active @endif"-->
            <!--                href="{{ route('service-offer') }}">-->
            <!--                <span class="menu-bullet">-->
            <!--                    <span class="bullet bullet-dot"></span>-->
            <!--                </span>-->
            <!--                <span class="menu-title">Services Offer</span>-->
            <!--            </a>-->
            <!--        </div>-->
                    
            <!--    </div>-->
            <!--</div>-->
            <!--@endif-->
            
            @if (access()->hasAccess(['wishlist']))
            <div class="menu-item">
                <a class="menu-link @if (request()->routeIs(['wishlist'])) active @elseif(request()->routeIs(['wishlist.view'])) active @endif"
                    href="{{ route('wishlist') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3"
                                    d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z"
                                    fill="currentColor" />
                                <path
                                    d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Wishlist</span>
                </a>
            </div>
            @endif


            @if (access()->hasAccess(['review']))
            <div class="menu-item">
                <a class="menu-link @if (request()->routeIs(['review'])) active @elseif(request()->routeIs(['review'])) active @endif"
                    href="{{ route('review') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3"
                                    d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z"
                                    fill="currentColor" />
                                <path
                                    d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Review</span>
                </a>
            </div>
            @endif

            @if (access()->hasAccess(['banner']))
                <div class="menu-item">
                    <div class="menu-content pt-8 pb-0">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Website</span>
                    </div>
                </div>
            @endif
            @if (access()->hasAccess(['enquiry']))
            <div class="menu-item">
                <a class="menu-link @if (request()->routeIs(['enquiry'])) active @endif"
                    href="{{ route('enquiry') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3"
                                    d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z"
                                    fill="currentColor" />
                                <path
                                    d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Enquiry</span>
                </a>
            </div>
            @endif
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs(['newsletter'])) active @endif"
                        href="{{ route('newsletter') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3"
                                        d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z"
                                        fill="currentColor" />
                                    <path
                                        d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Newsletter</span>
                    </a>
                </div>
      
            @if (access()->hasAccess(['banner']))
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs(['banner'])) active @endif"
                        href="{{ route('banner') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3"
                                        d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z"
                                        fill="currentColor" />
                                    <path
                                        d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Banners</span>
                    </a>
                </div>
            @endif
              
            <!--@if (access()->hasAccess(['warranty']))-->
            <!--    <div class="menu-item">-->
            <!--        <a class="menu-link @if (request()->routeIs(['warranty'])) active @endif"-->
            <!--            href="{{ route('warranty') }}">-->
            <!--            <span class="menu-icon">-->
            <!--                <span class="svg-icon svg-icon-2">-->
            <!--                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"-->
            <!--                        viewBox="0 0 24 24" fill="none">-->
            <!--                        <path opacity="0.3"-->
            <!--                            d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z"-->
            <!--                            fill="currentColor" />-->
            <!--                        <path-->
            <!--                            d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z"-->
            <!--                            fill="currentColor" />-->
            <!--                    </svg>-->
            <!--                </span>-->
            <!--            </span>-->
            <!--            <span class="menu-title">Warranty</span>-->
            <!--        </a>-->
            <!--    </div>-->
            <!--@endif-->
           
            @if (access()->hasAccess(['global', 'my-profile', 'users', 'roles']))
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion mb-1 @if (request()->routeIs(['global', 'my-profile', 'my-profile.*', 'users', 'roles'])) hover show @endif">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3"
                                        d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z"
                                        fill="currentColor" />
                                    <path
                                        d="M14.854 11.321C14.7568 11.2282 14.6388 11.1818 14.4998 11.1818H14.3333V10.2272C14.3333 9.61741 14.1041 9.09378 13.6458 8.65628C13.1875 8.21876 12.639 8 12 8C11.361 8 10.8124 8.21876 10.3541 8.65626C9.89574 9.09378 9.66663 9.61739 9.66663 10.2272V11.1818H9.49999C9.36115 11.1818 9.24306 11.2282 9.14583 11.321C9.0486 11.4138 9 11.5265 9 11.6591V14.5227C9 14.6553 9.04862 14.768 9.14583 14.8609C9.24306 14.9536 9.36115 15 9.49999 15H14.5C14.6389 15 14.7569 14.9536 14.8542 14.8609C14.9513 14.768 15 14.6553 15 14.5227V11.6591C15.0001 11.5265 14.9513 11.4138 14.854 11.321ZM13.3333 11.1818H10.6666V10.2272C10.6666 9.87594 10.7969 9.57597 11.0573 9.32743C11.3177 9.07886 11.6319 8.9546 12 8.9546C12.3681 8.9546 12.6823 9.07884 12.9427 9.32743C13.2031 9.57595 13.3333 9.87594 13.3333 10.2272V11.1818Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Authentication</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if (access()->hasAccess(['global']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['global'])) active @endif"
                                    href="{{ route('global') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title"> Global Settings </span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['my-profile']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['my-profile', 'my-profile.*'])) active @endif"
                                    href="{{ route('my-profile') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title"> My Account </span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['users']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['users'])) active @endif"
                                    href="{{ route('users') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Users</span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['roles']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['roles'])) active @endif"
                                    href="{{ route('roles') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title"> Roles </span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @if (access()->hasAccess(['homepage-setting-field','homepage-setting']))
            <div data-kt-menu-trigger="click"
                class="menu-item menu-accordion mb-1 @if (request()->routeIs(['homepage-setting-field','homepage-setting'])) hover show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3"
                                    d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z"
                                    fill="currentColor" />
                                <path
                                    d="M14.854 11.321C14.7568 11.2282 14.6388 11.1818 14.4998 11.1818H14.3333V10.2272C14.3333 9.61741 14.1041 9.09378 13.6458 8.65628C13.1875 8.21876 12.639 8 12 8C11.361 8 10.8124 8.21876 10.3541 8.65626C9.89574 9.09378 9.66663 9.61739 9.66663 10.2272V11.1818H9.49999C9.36115 11.1818 9.24306 11.2282 9.14583 11.321C9.0486 11.4138 9 11.5265 9 11.6591V14.5227C9 14.6553 9.04862 14.768 9.14583 14.8609C9.24306 14.9536 9.36115 15 9.49999 15H14.5C14.6389 15 14.7569 14.9536 14.8542 14.8609C14.9513 14.768 15 14.6553 15 14.5227V11.6591C15.0001 11.5265 14.9513 11.4138 14.854 11.321ZM13.3333 11.1818H10.6666V10.2272C10.6666 9.87594 10.7969 9.57597 11.0573 9.32743C11.3177 9.07886 11.6319 8.9546 12 8.9546C12.3681 8.9546 12.6823 9.07884 12.9427 9.32743C13.2031 9.57595 13.3333 9.87594 13.3333 10.2272V11.1818Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Homepage Setting</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    @if (access()->hasAccess(['homepage-setting-field']))
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['homepage-setting-field'])) active @endif"
                                href="{{ route('homepage-setting-field') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Homepage Setting Field </span>
                            </a>
                        </div>
                    @endif
                    @if (access()->hasAccess(['homepage-setting']))
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs(['homepage-setting'])) active @endif"
                            href="{{ route('homepage-setting') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Homepage Setting </span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        @endif
            @if (access()->hasAccess(['country', 'brands', 'city', 'state', 'quick-link', 'main_category', 'sub_category', 'pincode']))
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion @if (request()->routeIs(['country', 'quick-link', 'brands', 'city', 'state', 'main_category', 'sub_category'])) hover show @endif">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3"
                                        d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z"
                                        fill="currentColor" />
                                    <path
                                        d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Masters</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @if (access()->hasAccess(['brands']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['brands'])) active @endif"
                                    href="{{ route('brands') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Brand</span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['quick-link']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['quick-link'])) active @endif"
                                    href="{{ route('quick-link') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Quick Link</span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['city']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['city'])) active @endif"
                                    href="{{ route('city') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Cities</span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['country']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['country'])) active @endif"
                                    href="{{ route('country') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Country</span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['state']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['state'])) active @endif"
                                    href="{{ route('state') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">States</span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['pincode']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['pincode'])) active @endif"
                                    href="{{ route('pincode') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Pincode Management</span>
                                </a>
                            </div>
                        @endif

                        @if (access()->hasAccess(['main_category']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['main_category'])) active @endif"
                                    href="{{ route('main_category') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title"> Dynamic Categories </span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['sub_category']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['sub_category'])) active @endif"
                                    href="{{ route('sub_category') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title"> Dynamic SubCategories </span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['email-template']))
                            <div class="menu-item">
                                <a class="menu-link @if (request()->routeIs(['email-template'])) active @endif"
                                    href="{{ route('email-template') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title"> Email Template </span>
                                </a>
                            </div>
                        @endif
                        @if (access()->hasAccess(['variation']))
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['variation'])) active @endif"
                                href="{{ route('variation') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title"> Variation </span>
                            </a>
                        </div>
                    @endif
                    @if (access()->hasAccess(['variation-group']))
                        <div class="menu-item">
                            <a class="menu-link @if (request()->routeIs(['variation-group'])) active @endif"
                                href="{{ route('variation-group') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title"> Variation Group</span>
                            </a>
                        </div>
                    @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer">
    {{-- <span class="btn-label">@ Pixel</span> --}}
</div>
