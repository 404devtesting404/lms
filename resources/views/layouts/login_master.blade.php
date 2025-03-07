<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta id="csrf-token" name="csrf-token" content="{{ csrf_token() }}">
    <meta id="base_url" name="base_url" content="{{ url('/') }}">
    <meta id="ip_address" name="ip_address" content="{{ Request::ip() }}">

    <title>{{ config('app.name', 'LMS-AGFL') }}</title>

    @include('partials.login.inc_top')
</head>

<body>

@yield('content')


</body>

</html>
