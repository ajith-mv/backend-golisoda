
<div class="card-header" id="kt_activities_header">
    <h3 class="card-title fw-bolder text-dark">{{ $modal_title ?? 'Form Action' }}</h3>
    <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
            <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                        transform="rotate(-45 6 17.3137)" fill="currentColor" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                        transform="rotate(45 7.41422 6)" fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </button>
    </div>
</div>
<form id="add_discount_form" class="form" action="#" enctype="multipart/form-data">

    <div class="card-body position-relative" id="kt_activities_body">
        <div id="kt_activities_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
            data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
            <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll">
                <div class="fv-row mb-10">
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                        data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header"
                        data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">

                      
                        <input type="hidden" name="id" value="{{ $info->id ?? '' }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Discount Name</label>
                                    <input type="text" name="discount_name" class="form-control form-control-solid mb-3 mb-lg-0"
                                        placeholder="Discount Name" value="{{ $info->coupon_name ?? '' }}" />
                                </div>
                            </div>
                           
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Discount Type</label>
                                    
                                    <select name="calculate_type" id="calculate_type" aria-label="Select a Discount Type" data-control="select2" data-placeholder="Select Discount Type ..." class="form-select mb-2">
                                        <option value="percentage" @if(isset($info->calculate_type) && $info->calculate_type == "percentage") selected="selected" @endif>Percentage</option>
                                        <option value="fixed_amount" @if(isset($info->calculate_type) && $info->calculate_type == "fixed_amount") selected="selected" @endif>Fixed Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Discount Value</label>
                                    <input type="text" name="calculate_value" class="form-control form-control-solid mb-3 mb-lg-0 decimal"
                                        placeholder="Discount Value" value="{{ $info->calculate_value ?? '' }}" />
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Start Date</label>
                                    <input type="text" class="form-control form-control-solid" value="{{ $info->start_date ?? '' }}" placeholder="" id="start_date" name="start_date" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">End Date</label>
                                    <input type="text"  class="form-control form-control-solid"
                                        placeholder="" value="{{ $info->end_date ?? '' }}"  name="end_date" id="end_date" />
                                </div>
                            </div>
                            
                        </div>
                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Discount Applied for</label>
                                    <select name="discount_type" id="discount_type" aria-label="Select a Coupon Type" data-control="select2" data-placeholder="Select Coupon Type..." class="form-select mb-2">
                                        <option value="">Select a Coupon Type</option>
                                        {{-- <option value="1" @if(isset($info->coupon_type) && $info->coupon_type == "1") selected="selected" @endif>Product</option> --}}
                                        {{-- <option value="2" @if(isset($info->coupon_type) && $info->coupon_type == "2") selected="selected" @endif>Customer</option> --}}
                                        <option value="3" @if(isset($info->coupon_type) && $info->coupon_type == "3") selected="selected" @endif>Category</option>
                                        <option value="4" @if(isset($info->coupon_type) && $info->coupon_type == "4") selected="selected"  @endif>Product Collection</option>
                                    </select>
                                </div>
                            </div>
                       
                            <div class="col-md-6" id="couponData">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2" id="title">Select Item</label>
                                    <select name="product_id[]" id="product_id" aria-label="Select a item" multiple data-control="select2" data-placeholder="Select Item..." class="form-select mb-2">
                                        <option value="all" @if(isset($info->is_applied_all) && $info->is_applied_all == 'yes') selected="selected" @endif> All </option>
                                        @if(isset($couponTypeAttributes) && !empty( $couponTypeAttributes ) )
                                            @foreach ($couponTypeAttributes as $item)
                                                <option value="{{ $item->id }}"  
                                                    @if( isset($info->couponProducts) && in_array( $item->id, array_column( $info->couponProducts->toArray(), 'product_id'))  ) 
                                                    selected="selected" 
                                                    @elseif( isset($info->couponCustomers) && in_array( $item->id, array_column( $info->couponCustomers->toArray(), 'customer_id')) ) 
                                                    selected="selected" 
                                                    @elseif( isset($info->couponCategory) && in_array( $item->id, array_column( $info->couponCategory->toArray(), 'category_id')) )
                                                    selected="selected" 
                                                    @elseif( isset($info->couponProductCollection) && in_array( $item->id, array_column( $info->couponProductCollection->toArray(), 'product_collection_id')) )
                                                    selected="selected" 
                                                    @endif
                                                    >
                                                    {{ $item->name ?? $item->product_name ?? $item->first_name ?? $item->collection_name ?? '' }}
                                                </option>    
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Minimum Order Value</label>
                                    <input type="text" name="minimum_order_value" class="form-control form-control-solid mb-3 mb-lg-0 decimal"
                                        placeholder="Minimum Order Value" value="{{ $info->minimum_order_value ?? '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-bold fs-6 mb-2">Sorting Order</label>
                                    <input type="text" name="order_by" class="form-control form-control-solid mb-3 mb-lg-0 number"
                                        placeholder="Sorting Order" value="{{ $info->order_by ?? '' }}" />
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="fw-bold fs-6 mb-2"> Status </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
                                        <input class="form-check-input" type="checkbox"  name="status" value="1"  @if((isset( $info->status) && $info->status == 'published' ) || !isset($info->status)) checked @endif />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-5 text-center" id="kt_activities_footer">
        <div class="text-end px-8">
            <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
            <button type="submit" class="btn btn-primary" data-kt-order_status-modal-action="submit">
                <span class="indicator-label">Submit</span>
                <span class="indicator-progress">Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>
</form>
<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
<script>
        $(".number").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/\D/g, ""));
            if ((evt.which < 48 || evt.which > 57)) 
            {
                evt.preventDefault();
            }
        });

        $(".decimal").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g,''));
            if ((evt.which < 48 || evt.which > 57)) 
            {
                evt.preventDefault();
            }
        });

        function couponGendrate()
        {
            $.ajax({
                type: "GET",
                url: "{{ route('coupon.coupon-gendrate' ) }}",
                success: function(res) {
                    $('#coupon_code').val(res);
                }
            });
        }
        
        $('#discount_type').change(function(){

            var coupon_type = $("#discount_type").val();

            $.ajax({
                type: "POST",
                url: "{{ route('discount.coupon-apply' ) }}",
                data: {name:coupon_type},

                success: function(res) {
                    $('#title').html(res.title);
                    $('#product_id').html(res.data);
                    if( coupon_type == 2 ) {
                        $('#repeated_customer_count').show();
                    } else {
                        $('#repeated_customer_count').hide();
                    }
                }
            });
            
        })
       
        

        
</script>
<script>
    setTimeout(() => {
        $('#calculate_type').select2();
        $('#discount_type').select2();
        $('#product_id').select2();
    }, 200);
        


    var add_url = "{{ route('discount.save') }}";

    var KTUsersCoupon = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#add_discount_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);
        var initCoupon = () => {
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'calculate_type': {
                            validators: {
                                notEmpty: {
                                    message: 'Calculate Type is required'
                                }
                            }
                        },
                        'discount_name': {
                            validators: {
                                notEmpty: {
                                    message: 'Discount Name is required'
                                }
                            }
                        },
                        
                        'calculate_value': {
                            validators: {
                                notEmpty: {
                                    message: 'Discount Value is required'
                                }
                            }
                        },
                        'start_date': {
                            validators: {
                                notEmpty: {
                                    message: 'Start Date is required'
                                }
                            }
                        },
                        'end_date': {
                            validators: {
                                notEmpty: {
                                    message: 'End Date is required'
                                }
                            }
                        },
                        'discount_type': {
                            validators: {
                                notEmpty: {
                                    message: 'Discount Type is required'
                                }
                            }
                        },
                        'product_id[]': {
                            validators: {
                                notEmpty: {
                                    message: 'Select Item is required'
                                }
                            }
                        },
                        'minimum_order_value': {
                            validators: {
                                notEmpty: {
                                    message: 'Minimum Order Value is required'
                                }
                            }
                        },
                        'order_by': {
                            validators: {
                                notEmpty: {
                                    message: 'Shortin Order is required'
                                }
                            }
                        },
                        
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.fv-row',
                            eleInvalidClass: '',
                            eleValidClass: ''
                        }),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'fa fa-check',
                            invalid: 'fa fa-times',
                            validating: 'fa fa-refresh',
                        }),
                    }
                }
            );
            const cancelButton = element.querySelector('#discard');
            cancelButton.addEventListener('click', e => {
                e.preventDefault();

                Swal.fire({
                    text: "Are you sure you would like to cancel?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, cancel it!",
                    cancelButtonText: "No, return",
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: "btn btn-active-light"
                    }
                }).then(function(result) {
                    if (result.value) {
                        commonDrawer.hide(); // Hide modal				
                    }
                });
            });
            const submitButton = element.querySelector('[data-kt-order_status-modal-action="submit"]');
            submitButton.addEventListener('click', function (e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        
                        if (status == 'Valid') {
                           
                            var form = $('#add_discount_form')[0]; 
                            var formData = new FormData(form);
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            submitButton.disabled = true;
                            //call ajax call
                            $.ajax({
                                url: add_url,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                beforeSend: function() {},
                                success: function(res) {
                                    if (res.error == 1) {
                                        // Remove loading indication
                                        submitButton.removeAttribute('data-kt-indicator');
                                        // Enable button
                                        submitButton.disabled = false;
                                        let error_msg = res.message
                                        Swal.fire({
                                            text: res.message,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        });
                                    } else {
                                       
                                        dtTable.ajax.reload();
                                        Swal.fire({
                                            text: res.message,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then(function(result) {
                                            if (result
                                                .isConfirmed) {
                                                commonDrawer
                                                    .hide();

                                            }
                                        });
                                    }
                                }
                            });

                        } else {
                            // Show popup warning. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                text: "Sorry, looks like there are some errors detected, please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                }
            });
        
        var endDate = $(form.querySelector('[name="end_date"]'));
		    endDate.flatpickr({
			enableTime: false,
			dateFormat: "Y-m-d",
		});
        document.getElementById("start_date").flatpickr({
            
            minDate: "today",
            enableTime: false,
			dateFormat: "Y-m-d",
            onChange: function(dateObj, dateStr, instance) {
                    var StartDate = $("#start_date").val(); 
                    var endDate = $(form.querySelector('[name="end_date"]'));
                    endDate.flatpickr({
                    minDate: StartDate,
                    enableTime: false,
                    dateFormat: "Y-m-d",
                });
            },
           
        });


        }
        return {
            init: function() {
                initCoupon();
            }
        };


    }();
    KTUtil.onDOMContentLoaded(function() {
        KTUsersCoupon.init();
    });

</script>