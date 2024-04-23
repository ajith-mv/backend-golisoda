@component('mail::layout')
  @slot('header')
        @component('mail::header', ['url' => config('app.website_url')])
            <img src="https://admin.golisodastore.com/public/assets/global_setting/logo/1707472536_logo.png" alt="{{ config('app.name') }} Logo">
        @endcomponent
    @endslot

               {!! $data !!}


@slot('footer')
        @component('mail::footer')
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        @endcomponent
    @endslot
@endcomponent
