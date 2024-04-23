@if (isset($data) && !empty($data))
    @foreach ($data as $item)
        <div class="col-md-4">
            <!--begin::Card-->
            <div class="card card-flush h-md-100">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>{{ $item->name }}</h2>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-1">
                    @php
                        $permission = unserialize($item->permissions);
                        // echo '<pre>';
                        // print_r( $permission );
                        // echo '</pre>';
                    @endphp
                    <!--begin::Users-->
                    <div class="fw-bolder text-gray-600 mb-5">Total users with this role: {{ count($permission) }}</div>
                    <!--end::Users-->
                    <!--begin::Permissions-->
                   
                    <div class="d-flex flex-column text-gray-600">
                        @if( isset( $permission ) && !empty( $permission ) )
                            @php
                                $count = 0;
                                $limit = count( $permission );
                                $remain = 0;
                                if( count($permission) > 5 ) {
                                    $limit = 5;
                                    $remain = count($permission) - 5;
                                }
                            @endphp
                            @foreach ($permission as $perkey => $perValue)
                                @php
                                   
                                    $message = [];
                                    foreach ($perValue as $key => $value) {
                                        if( $value == 'on' ) {
                                            $message[] = ' '.ucfirst(str_replace($perkey.'_', "", $key));
                                        }
                                        
                                    }
                                    
                                    $message = implode(',', $message ).' for '.$perkey;
                                   
                                    // print_r( $message );
                                @endphp
                                {{-- <div class="d-flex align-items-center py-2">
                                    <span class="bullet bg-primary me-3">  for </span>
                                </div> --}}
                                <div class="d-flex align-items-center py-2">
                                    <span class="bullet bg-primary me-3"></span>{{ $message }}
                                </div>
                            @endforeach
                           
                            @if ($limit > 5 )
                                <div class='d-flex align-items-center py-2'>
                                    <span class='bullet bg-primary me-3'></span>
                                    <em>and {{ $remain }} more...</em>
                                </div>
                            @endif
                        @endif
                       
                    </div>
                    <!--end::Permissions-->
                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer flex-wrap pt-0">
                    <a href="{{ route('roles.view', ['id' => $item->id ]) }}" class="btn btn-light btn-active-primary my-1 me-2">View Role</a>
                    <button type="button" class="btn btn-light btn-active-light-primary my-1" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_update_role" onclick="return openForm('{{ $item->id }}')">Edit Role</button>
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->
        </div>
    @endforeach
@endif
