<div class="d-flex flex-column ">
        <div class="row">
            <div class="col-md-10">
                <h3>Customer Address</h3>
            </div>
            @if( auth()->user()->is_super_admin )
            <div class="col-md-2">
                {{-- <a class="btn btn-primary">Add <i class="fa fa-plus"></i> </a> --}}
                <button type="button" class="btn btn-primary" onclick="addCustomer({{ $info->id }})">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                fill="currentColor" />
                        </svg>
                    </span>
                    Add
                </button>
            </div>
            @endif
        </div>
        <div class="card-body" id="address_list_pane">
            <!--begin::Addresses-->
            @include('platform.customer.form.address._list')
        </div>
</div>
@section('add_on_script')
    <script>
        listAddress('{{ $info->id ?? '' }}');
        function addCustomer(customerId, addressId = ''){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            $.ajax({
                url: '{{route("customer.add-address")}}',
                type: 'get',
                data:{customerId:customerId,addressId:addressId},
                success: function(res) {
                    $( '#form-common-content' ).html(res);
                    const drawerEl = document.querySelector("#kt_common_add_form");
                    const commonDrawer = KTDrawer.getInstance(drawerEl);
                    commonDrawer.show();
                    return false;
                }
            });
        }
        
        function deleteCustomer(customerId, addressId = '') {
        Swal.fire({
            text: "Are you sure you would like to delete?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, return",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then(function (result) {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route("customer.delete") }}',
                    type: 'POST',
                    data: {addressId:addressId},
                    success: function(res) {
                        listAddress(customerId)
                        Swal.fire({
                            title: "Deleted!",
                            text: res.message,
                            icon: "success",
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-success"
                            },
                            timer: 3000
                        });
                        
                    }
                });		
            }
        });
    }
        function listAddress(customerId){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            $.ajax({
                url: '{{route("customer.address.list")}}',
                type: 'POST',
                data:{customerId:customerId},
                success: function(res) {
                   $('#address_list_pane').html( res );
                }
            });
        }
    </script>
@endsection
