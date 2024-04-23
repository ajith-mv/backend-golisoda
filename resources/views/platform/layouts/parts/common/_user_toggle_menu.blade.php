<div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
    @if( isset(auth()->user()->image) && !empty( auth()->user()->image ) )
        <img alt="user" src="{{ asset('/') . auth()->user()->image }}" />
    @else                    
        <img alt="user" src="{{ asset('userImage/no_Image.jpg') }}" />
    @endif
    
</div>

<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
    
    <div class="menu-item px-3">
        <div class="menu-content d-flex align-items-center px-3">
            
            <div class="symbol symbol-50px me-5">
                @if( isset(auth()->user()->image) && !empty( auth()->user()->image ) )
                    <img alt="Logo" src="{{ asset('/') . auth()->user()->image }}" />
                @else                    
                    <img alt="Logo" src="{{ asset('userImage/no_Image.jpg') }}" />
                @endif
            </div>
            
            <div class="d-flex flex-column">
                <div class="fw-bolder d-flex align-items-center fs-5">
                    {{ auth()->user()->name ?? '' }}
                    {{-- <span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2">Pro</span> --}}
                </div>
                <a href="#" class="fw-bold text-muted text-hover-primary fs-7">{{ auth()->user()->email ?? '' }}</a>
            </div>
            <!--end::Username-->
        </div>
    </div>
  
    <div class="separator my-2"></div>
  
    <div class="menu-item px-5">
        <a href="{{ route('my-profile') }}" class="menu-link px-5">My Profile</a>
    </div>
    <div class="separator my-2"></div>
    <div class="menu-item px-5 my-1">
        <a href="{{ route('my-profile.password') }}" class="menu-link px-5">Change Password</a>
    </div>
    <div class="menu-item px-5">
        <a href="{{ route('logout') }}" class="menu-link px-5"  onclick="event.preventDefault();
        document.getElementById('logout-form').submit();">Sign Out</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form> 
    </div>
</div>