<div class="card-toolbar w-100">
    <div class="col-sm-12">
        <h4> Filter Reports </h4>
    </div>
    <form id="search-form">
        <div class="row w-100">
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="form-group">
                    <label class="text-muted"> Date Added </label>
                    <input class="form-control form-control-solid w-100 date_range" name="date_range" placeholder="Pick date range" id="kt_ecommerce_report_views_daterangepicker" />
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label class="text-muted"> Vendor Name </label>

                    <select name="filter_search_data" id="filter_search_data" aria-label="Select a Vendor"  data-placeholder="Select a vendor..." class="form-select mb-2">
                        <option value="">Select a Vendor</option>
                        @isset($vendors)
                            @foreach ($vendors as $item)
                                <option value="{{ $item->id }}" >
                                    {{ $item->brand_name }}
                                </option>
                            @endforeach
                        @endisset
                    </select>

{{--                    <input type="text" name="filter_search_data" id="filter_search_data" class="form-control">--}}
                </div>
            </div>

            
            <div class="col-sm-6 col-md-4 col-lg-4">
                <div class="form-group mt-8 text-start">
                    <button type="reset" class="btn btn-sm btn-warning" > Clear </button>
                    <button type="submit" class="btn btn-sm btn-primary" > Submit </button>
                </div>
            </div>
        </div>
    </form>
</div>

