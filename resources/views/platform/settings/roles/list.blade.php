<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
    <x-role :data="$data"></x-role>
    <div class="ol-md-4">
        <div class="card h-md-100">
            <div class="card-body d-flex flex-center">
                <button type="button" class="btn btn-clear d-flex flex-column flex-center"  onclick="return openForm()" >
                    <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}" alt="" class="mw-100 mh-150px mb-7">
                    <div class="fw-bolder fs-3 text-gray-600 text-hover-primary">Add New Role</div>
                </button>
            </div>
        </div>
    </div>
</div>