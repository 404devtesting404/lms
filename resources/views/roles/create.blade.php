@extends('layouts.master')
@section('page_title', 'User Roles')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Roles</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ url('roles/store') }}"  role="form" enctype="multipart/form-data">
                @csrf

                @include('roles.form')

            </form>
        </div>
    </div>

@endsection
