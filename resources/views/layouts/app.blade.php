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
<body class="bg-white">
@if(empty($fullscreen))
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand mb-0 h1" href="/">{{ config('app.name', 'GeoLV') }}</a>
        @auth
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item{{ request()->is('/')? ' active': '' }}">
                    <a class="nav-link" href="{{ route('home') }}">{{ __('Geocode Address') }}</a>
                </li>
                <li class="nav-item{{ request()->is('/files')? ' active': '' }}">
                    <a class="nav-link" href="{{ route('files.index') }}">{{ __('Geocode File') }}</a>
                </li>
                @can('viewUsers')
                    <li class="nav-item{{ request()->is('/users')? ' active': '' }}">
                        <a class="nav-link" href="{{ route('users.index') }}">{{ __('Users') }}</a>
                    </li>
                @endcan
                @can('viewTelescope')
                    <li class="nav-item{{ request()->is('/users')? ' active': '' }}">
                        <a class="nav-link" href="{{ route('telescope') }}" target="_blank">Debug</a>
                    </li>
                @endcan
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item mr-2">
                    <span class="navbar-text">{{ __('Hello, :name', ['name' => auth()->user()->name]) }}!</span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-primary btn-block" href="{{ route('logout') }}"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        {{ __('Logout') }} <i class="fa fa-sign-out ml-2"></i>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
        @endauth
    </nav>
@endif

@yield('content')

<script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>