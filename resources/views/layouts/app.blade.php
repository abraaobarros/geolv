<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GeoLV') }}</title>
</head>
<body>
@if(empty($fullscreen))
<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
    <a href="/" class="h2 my-0 mr-md-4 font-weight-normal">{{ config('app.name') }}</a>
    @auth
        <nav class="my-2 my-md-0 mr-md-auto">
            <a class="p-2 text-dark{{ request()->is('/')? ' active': '' }}" href="{{ route('home') }}">
                {{ __('Geocode Address') }}
            </a>
            <a class="p-2 text-dark{{ request()->is('/files')? ' active': '' }}" href="{{ route('files.index') }}">
                {{ __('Geocode File') }}
            </a>
        </nav>
        <div class="my-2 my-md-0 mr-md-3">
            {{ __('Hello, :NAME', auth()->user()->name) }}!
        </div>
        <a class="btn btn-outline-primary" href="{{ route('logout') }}"
           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            {{ __('Logout') }} <i class="fa fa-sign-out ml-2"></i>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @endauth
</div>
@endif

@yield('content')

<script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>