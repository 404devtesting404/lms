@extends('layouts.master')
@section('page_title', 'Financing Type')
@section('content')


<div class="card card-default">
    <div class="card-header">
        <span class="card-title">Edit Role Form</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('roles/update', $role->id) }}"  role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf

            @include('loan-type.form')

        </form>
    </div>
</div>
@endsection
