@extends('layouts.login_master')
@section('content')
 <style>
     .login_bg{
         background-image: linear-gradient(90deg, rgb(41, 170, 101) 35%, rgb(72, 170, 41) 100%);
     }

 </style>
    <div class="page-content login_bg">
        <div class="content-wrapper">
            <div class="content d-flex justify-content-center align-items-center">
                <form class="login-form" method="post" action="{{ route('login') }}">
                    @csrf
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center mb-3">
                               <img src="{{asset('/public/global_assets/images/logo.png')}}">

                                <span class="d-block text-muted">Your credentials</span>
                            </div>
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-styled-left alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                        <span class="font-weight-semibold">Oops!</span> {{ implode('<br>', $errors->all()) }}
                                    </div>
                                @endif
                            <div class="form-group ">
                                <input type="text" class="form-control" name="identity" value="{{ old('identity') }}" placeholder="Login ID or Email">
                            </div>
                            <div class="form-group ">
                                <input required name="password" type="password" class="form-control" placeholder="{{ __('Password') }}">

                            </div>
                            <div class="form-group">
                                <input name="latitude" type="hidden" id="latitude_in">
                                <input name="longitude" type="hidden" id="longitude_in">
                                <button type="submit" class="btn btn-warning btn-block">Sign in <i class="icon-circle-right2 ml-2"></i></button>
                            </div>
                            </div>
                    </div>
                </form>

            </div>


        </div>

    </div>
    @endsection
