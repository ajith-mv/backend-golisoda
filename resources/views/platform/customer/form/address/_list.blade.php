<div class="card mb-5 mb-xl-10">
    <div class="">
        <div class="row gx-9 gy-6">
            @isset($customerAddress)
                @foreach ($customerAddress as $key=>$val )
                
                <div class="col-xl-6">
                    <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                        <div class="d-flex flex-column py-2">
                            <div class="d-flex align-items-center fs-4 fw-bolder mb-5">{{ $val->name }} 
                                <?php if($val->is_default == 1){?> 
                                    <span class='badge badge-light-success fs-7 ms-2'>Default</span>
                                <?php }?>
                                    <span class='badge badge-light-danger fs-7 ms-2'>{{  $val->subCategory->name }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div>
                                    <br /><strong>Email : </strong>{{ $val->email }}
                                    <br /><strong>Mobile : </strong>{{ $val->mobile_no }}
                                    <br/><strong>Address : </strong>{{ $val->address_line1 }}
                                    <br/><strong>Country : </strong>{{ $val->countries->name ?? null }}
                                    <br/><strong>State : </strong>{{ $val->states->state_name ?? null }}
                                    <br/><strong>City : </strong>{{ $val->city->city ?? null }}
                                    <br/><strong>Pincode : </strong>{{ $val->pincode->pincode ?? null }}
                                </div>
                            </div>
                        </div>
                        @if( auth()->user()->is_super_admin )
                        <div class="d-flex align-items-center py-2">
                            <button type="button" class="btn btn-sm btn-light btn-active-light-primary me-3" onclick="addCustomer('{{ $info->id }}','{{ $val->id }}')">Edit</button>
                            <button type="button" class="btn btn-sm btn-light btn-active-light-primary me-3" onclick="deleteCustomer('{{ $info->id }}','{{ $val->id }}')">Delete</button>
                       </div>
                       @endif
                    </div>
                </div>
                @endforeach
            @endisset
        </div>
    </div>
</div>