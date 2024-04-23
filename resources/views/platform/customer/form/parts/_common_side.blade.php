<style>
    .circular--landscape {
         display: inline-block;
        position: relative;
        width: 200px;
        height: 200px;
        overflow: hidden;
        border-radius: 50%;
    } 
    .circular--landscape img {
        width: auto;
        height: 100%;
        margin-left: -50px; 
    }
</style>
<div class="card card-flush" >
    <div class="card-header">
        <div class="card-title w-100 mt-3">
            <h2 class="w-100">
                <strong for="">{{  $info->first_name." ".$info->last_name}}</strong>
            </h2>
        </div>
        <div class="row w-100">
            <div class="col-sm-2">
                @if ($info->profile_image ?? '')
                @php 
                    $path = asset(Storage::url($info->profile_image,'public'));
                @endphp
                @else
                    @php
                        $path = asset('userImage/no_Image.jpg');
                    @endphp
                @endif
                <img src="{{ $path }}" width="75" alt="Avatar">
            </div>
            <div class="col-sm-4">
                <div>
                    <label for="">{{  $info->customer_no}}</label>
                </div>
                <div>
                    <label for="">{{  $info->email}}</label>
                </div>
                <div>
                    <label for="">{{  $info->mobile_no}}</label>
                </div>
            </div>
        </div>
    </div>   
   
</div>
