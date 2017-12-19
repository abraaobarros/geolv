<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <title>GeoLV</title>
</head>
<body>
    @yield('content')

    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>