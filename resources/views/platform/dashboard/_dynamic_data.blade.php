<div class="row gy-5 g-xl-10">

    <div class="col-md-4">
        <div class="card overflow-hidden mb-5 mb-xl-10">
            <div class="card-body d-flex justify-content-between flex-column px-0 py-16 pb-0">
                <div class="mb-11 px-9">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2">{{ $total_order ?? 0 }}</span>
                        <span class="d-flex align-items-end text-gray-400 fs-6 fw-bold"></span>
                    </div>
                    <span class="fs-6 fw-bold text-gray-400">Total Sales</span>
                </div>
                <div class="mb-11 px-9">
                    <div class="d-flex align-items-center mb-2">
                        <span
                            class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2">{{ $total_product ?? 0 }}</span>
                    </div>
                    <span class="fs-6 fw-bold text-gray-400">Total Products</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-4 mb-xl-2">
        <div class="card overflow-hidden mb-5 mb-xl-10">
            <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                <div class="row py-4">
                    <div class="col-sm-6">
                        <div class="mb-11 px-9">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2">
                                    {{ $total_payment ?? 0 }} </span>
                                <span class="d-flex align-items-end text-gray-400 fs-6 fw-bold"></span>
                            </div>
                            <span class="fs-6 fw-bold text-gray-400"> Total Payments </span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-11 px-9">
                            <div class="d-flex align-items-center mb-2">
                                <span
                                    class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2">{{ $total_customer ?? 0 }}</span>
                                <span class="d-flex align-items-end text-gray-400 fs-6 fw-bold"></span>
                            </div>
                            <span class="fs-6 fw-bold text-gray-400">Total Customers</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-11 px-9">
                            <div class="d-flex align-items-center mb-2">
                                <span
                                    class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2">{{ $total_success_order ?? 0 }}</span>
                                <span class="d-flex align-items-end text-gray-400 fs-6 fw-bold"></span>
                            </div>
                            <span class="fs-6 fw-bold text-gray-400">Total Success</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-11 px-9">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-2hx fw-bolder text-gray-800 me-2 lh-1 ls-n2">
                                    {{ $total_fail_order ?? 0 }} </span>
                                <span class="d-flex align-items-end text-gray-400 fs-6 fw-bold"></span>
                            </div>
                            <span class="fs-6 fw-bold text-gray-400">Total Failures</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (isset($new_customer) && !empty($new_customer))
        <div class="col-md-4">
            <div class="card card-flush mb-lg-10">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <div class="card-title d-flex flex-column">
                        <!--begin::Amount-->
                        <span class="fs-2hx fw-bolder text-dark me-2 lh-1 ls-n2">{{ count($new_customer) }}</span>
                        <!--end::Amount-->
                        <!--begin::Subtitle-->
                        <span class="text-gray-400 pt-1 fw-bold fs-6">New Customers This Month</span>
                        <!--end::Subtitle-->
                    </div>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body d-flex flex-column justify-content-end pe-0 my-6">
                    <!--begin::Title-->
                    <span class="fs-6 fw-boldest text-gray-800 d-block mb-2"> Today’s Heroes</span>
                    <!--end::Title-->
                    <!--begin::Users group-->
                    <div class="symbol-group symbol-hover flex-nowrap">
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($new_customer as $item)
                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                title="{{ $item->first_name }}">
                                @if ($info->profile_image ?? '')
                                    @php
                                        $path = Storage::url($info->profile_image, 'public');
                                    @endphp
                                    <img alt="Pic" src="{{ asset($path) }}" />
                                @else
                                    <span
                                        class="symbol-label bg-warning text-inverse-warning fw-bolder">{{ ucfirst(substr($item->first_name, 0, 1)) }}</span>
                                @endif
                            </div>
                            @php
                                $i++;
                                if ($i == 10) {
                                    break;
                                }
                            @endphp
                        @endforeach
                    </div>
                    <!--end::Users group-->
                </div>
                <!--end::Card body-->
            </div>
        </div>
    @endif
    <!--end::Col-->
</div>
<div class="row gy-5 g-xl-10">
    @if( isset( $recent_order ) && !empty( $recent_order ) ) 
    <div class="col-xl-4 mb-xl-10">
        <div class="card card-flush">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-gray-800">Recent Order</span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ url('order') }}" class="btn btn-sm btn-light" data-bs-toggle='tooltip' data-bs-dismiss='click'
                        data-bs-custom-class="tooltip-dark" title="View all orders">View All</a>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="kt_list_widget_10_tab_1">
                        <div class="m-0">
                            <div class="d-flex align-items-sm-center mb-5">
                                <div class="symbol symbol-45px me-4">
                                    <span class="symbol-label bg-primary">
                                        <span class="svg-icon svg-icon-2x svg-icon-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3"
                                                    d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <a href="#" class="text-gray-400 fs-6 fw-bold">Order No</a>
                                        <span class="text-gray-800 fw-bolder d-block fs-4">#{{ $recent_order->order_no }}</span>
                                    </div>
                                    <span
                                        class="badge badge-lg badge-light-success fw-bolder my-2">{{ ucwords($recent_order->status) }}</span>
                                </div>
                            </div>
                            <div class="timeline">
                                <div class="timeline-item align-items-center mb-7">
                                    <div class="timeline-line w-40px mt-6 mb-n12"></div>
                                    <div class="timeline-icon" style="margin-left: 11px">
                                        <span class="svg-icon svg-icon-2 svg-icon-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3"
                                                    d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="timeline-content m-0">
                                        <span class="fs-6 text-gray-400 fw-bold d-block">{{ $recent_order->billing_name }}</span>
                                        <span class="fs-6 fw-bolder text-gray-800">{{ $recent_order->billing_address_line1 ?? '' }}</span>
                                        <span class="fs-6 fw-bolder text-gray-800">{{ $recent_order->billing_email ?? '' }}</span>
                                        <span class="fs-6 fw-bolder text-gray-800">{{ $recent_order->billing_mobile_no ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="timeline">
                            <div class="timeline-item align-items-center mb-7">
                                <div class="timeline-line w-40px mt-6 mb-n12"></div>
                                <div class="timeline-icon" style="margin-left: 11px">
                                    <span class="svg-icon svg-icon-2 svg-icon-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3"
                                                d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z"
                                                fill="currentColor" />
                                            <path
                                                d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="timeline-content m-0">
                                    <span class="fs-6 text-gray-400 fw-bold d-block">{{ date('d-M-Y H:i A', strtotime($recent_order->created_at)) }}</span>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="separator separator-dashed my-6"></div> --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-xl-8 mb-5 mb-xl-10">
        <div class="row g-5 g-xl-10 h-xxl-50 mb-0 mb-xl-10">
            <div class="col-xxl-12">
                <div class="card card-flush h-lg-100">
                    <div class="card-header py-7 mb-3">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-gray-800">Top Selling Categories</span>
                        </h3>
                    </div>
                    <div class="card-body py-0 ps-6 mt-n12">
                        <div id="kt_charts_widget_6"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-12 mb-5 mb-xl-0">
                <div class="card card-flush h-lg-100">
                    <div class="card-header py-7 mb-3">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder text-gray-800">Top Selling Products</span>
                        </h3>
                    </div>
                    <div class="card-body py-0 ps-6 mt-n12">
                        <div id="kt_charts_selling_products"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    @php
        $sale_values = $top_selling_category['amount'];
        $categories = $top_selling_category['categories'];

        $saleProduct_values = $top_selling_product['amount'];
        $products = $top_selling_product['categories'];
    @endphp
    <script>
        var sale_values = @json($sale_values);
        var categories = @json($categories);
        var saleProduct_values = @json($saleProduct_values);
        var products = @json($products);
        // Class definition
        var KTChartsWidget6 = function() {
            // Private methods
            var initChart = function() {
                var element = document.getElementById("kt_charts_widget_6");

                if (!element) {
                    return;
                }

                var labelColor = KTUtil.getCssVariableValue('--bs-gray-800');
                var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');
                var maxValue = 18;

                var options = {
                    series: [{
                        name: 'Sales',
                        data: sale_values
                    }],
                    chart: {
                        fontFamily: 'inherit',
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 8,
                            horizontal: true,
                            distributed: true,
                            barHeight: 50,
                            dataLabels: {
                                position: 'bottom' // use 'bottom' for left and 'top' for right align(textAnchor)
                            }
                        }
                    },
                    dataLabels: { // Docs: https://apexcharts.com/docs/options/datalabels/
                        enabled: true,
                        textAnchor: 'start',
                        offsetX: 0,
                        formatter: function(val, opts) {
                            var val = val * 1000;
                            var Format = wNumb({
                                //prefix: '$',
                                //suffix: ',-',
                                thousand: ','
                            });

                            return Format.to(val);
                        },
                        style: {
                            fontSize: '14px',
                            fontWeight: '600',
                            align: 'left',
                        }
                    },
                    legend: {
                        show: false
                    },
                    colors: ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA'],
                    xaxis: {
                        categories: categories,
                        labels: {
                            formatter: function(val) {
                                return val + "K"
                            },
                            style: {
                                colors: labelColor,
                                fontSize: '14px',
                                fontWeight: '600',
                                align: 'left'
                            }
                        },
                        axisBorder: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val, opt) {
                                if (Number.isInteger(val)) {
                                    var percentage = parseInt(val * 100 / maxValue).toString();
                                    return val + ' - ' + percentage + '%';
                                } else {
                                    return val;
                                }
                            },
                            style: {
                                colors: labelColor,
                                fontSize: '14px',
                                fontWeight: '600'
                            },
                            offsetY: 2,
                            align: 'left'
                        }
                    },
                    grid: {
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: false
                            }
                        },
                        strokeDashArray: 4
                    },
                    tooltip: {
                        style: {
                            fontSize: '12px'
                        },
                        y: {
                            formatter: function(val) {
                                return val + 'K';
                            }
                        }
                    }
                };

                var chart = new ApexCharts(element, options);

                // Set timeout to properly get the parent elements width
                setTimeout(function() {
                    chart.render();
                }, 200);
            }

            // Public methods
            return {
                init: function() {
                    initChart();
                }
            }
        }();

        // Class definition
        var kt_charts_selling_products = function() {
            // Private methods
            var initChart = function() {
                var element = document.getElementById("kt_charts_selling_products");

                if (!element) {
                    return;
                }

                var labelColor = KTUtil.getCssVariableValue('--bs-gray-800');
                var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');
                var maxValue = 18;

                var options = {
                    series: [{
                        name: 'Sales',
                        data: saleProduct_values
                    }],
                    chart: {
                        fontFamily: 'inherit',
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 8,
                            horizontal: true,
                            distributed: true,
                            barHeight: 50,
                            dataLabels: {
                                position: 'bottom' // use 'bottom' for left and 'top' for right align(textAnchor)
                            }
                        }
                    },
                    dataLabels: { // Docs: https://apexcharts.com/docs/options/datalabels/
                        enabled: true,
                        textAnchor: 'start',
                        offsetX: 0,
                        formatter: function(val, opts) {
                            var val = val * 1000;
                            var Format = wNumb({
                                //prefix: '$',
                                //suffix: ',-',
                                thousand: ','
                            });

                            return Format.to(val);
                        },
                        style: {
                            fontSize: '14px',
                            fontWeight: '600',
                            align: 'left',
                        }
                    },
                    legend: {
                        show: false
                    },
                    colors: ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA'],
                    xaxis: {
                        categories: products,
                        labels: {
                            formatter: function(val) {
                                return val + "K"
                            },
                            style: {
                                colors: labelColor,
                                fontSize: '14px',
                                fontWeight: '600',
                                align: 'left'
                            }
                        },
                        axisBorder: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val, opt) {
                                if (Number.isInteger(val)) {
                                    var percentage = parseInt(val * 100 / maxValue).toString();
                                    return val + ' - ' + percentage + '%';
                                } else {
                                    return val;
                                }
                            },
                            style: {
                                colors: labelColor,
                                fontSize: '14px',
                                fontWeight: '600'
                            },
                            offsetY: 2,
                            align: 'left'
                        }
                    },
                    grid: {
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: false
                            }
                        },
                        strokeDashArray: 4
                    },
                    tooltip: {
                        style: {
                            fontSize: '12px'
                        },
                        y: {
                            formatter: function(val) {
                                return val + 'K';
                            }
                        }
                    }
                };

                var chart = new ApexCharts(element, options);

                // Set timeout to properly get the parent elements width
                setTimeout(function() {
                    chart.render();
                }, 200);
            }

            // Public methods
            return {
                init: function() {
                    initChart();
                }
            }
        }();

        // Webpack support
        if (typeof module !== 'undefined') {
            module.exports = KTChartsWidget6;
            module.exports = kt_charts_selling_products;
        }

        // On document ready
        KTUtil.onDOMContentLoaded(function() {
            KTChartsWidget6.init();
            kt_charts_selling_products.init();
        });
</script>