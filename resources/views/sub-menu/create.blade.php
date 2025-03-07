@extends('layouts.master')
@section('page_title', 'Sub Menu')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Sub Menu</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ url('/storeSubMenu') }}"  role="form" enctype="multipart/form-data">
                @csrf

                @include('sub-menu.form')

            </form>
        </div>
    </div>

@endsection
