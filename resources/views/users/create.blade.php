@extends('layouts.master')
@section('page_title', 'Create Users')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create User</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{url('users/store')}}"  role="form" enctype="multipart/form-data">
                @csrf

                @include('users.form')

            </form>
        </div>
    </div>

@endsection
