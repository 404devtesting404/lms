@extends('layouts.master')
@section('page_title', 'Main Menu')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Main Menu</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{url('/storeMainMenu')}}"  role="form" enctype="multipart/form-data">
                @csrf

                @include('main-menu.form')

            </form>
        </div>
    </div>

@endsection
