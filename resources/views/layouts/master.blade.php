<?php
use App\Helpers\Qs;
$request = request();
Qs::storelogs($request);
if(Auth::user()->user_type != 'super_admin'){
$page_id = Qs::pageCheck()['page_id'];
$sub_menu_ids = Qs::pageCheck()['sub_menu_ids'];
}

?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta id="csrf-token" name="csrf-token" content="{{ csrf_token() }}">
    <meta id="base_url" name="base_url" content="{{ url('/') }}">
    <meta id="user_id" name="user_id" content="{{ Auth::user()->id }}">
    <meta id="ip_address" name="ip_address" content="{{ Request::ip() }}">
    <title> @yield('page_title') | {{ config('app.name') }} </title>
    @include('partials.inc_top')
    <style>
        .c-anchor{
            color: #2e8b57;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>

<body class="{{ in_array(Route::currentRouteName(), ['payments.invoice', 'marks.tabulation', 'marks.show', 'ttr.manage', 'ttr.show']) ? 'sidebar-xs' : '' }}">

@include('partials.top_menu')
<div class="page-content">
    @include('partials.menu')
    <div class="content-wrapper">
        @include('partials.header')

        <div class="content">

            {{--Error Alert Area--}}
            @if($errors->any())
                <div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                        @foreach($errors->all() as $er)
                            <span><i class="icon-arrow-right5"></i> {{ $er }}</span> <br>
                        @endforeach

                </div>
            @endif
            <div id="ajax-alert" style="display: none"></div>

            @if(Auth::user()->user_type != 'super_admin')

                @if(in_array($page_id,$sub_menu_ids))

                    @yield('content')

                @else

                    <p style="color:red;">Permission denied...</p>

                @endif

            @else

                @yield('content')

            @endif
        </div>


    </div>
</div>

@include('partials.inc_bottom')
@include('partials.modal')
@yield('scripts')
</body>
</html>
