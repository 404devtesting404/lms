@extends('layouts.master')
@section('page_title', 'Company Legal')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Company Legal</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('company-legals.store') }}"  role="form" enctype="multipart/form-data">
                @csrf

                @include('company-legal.form')

            </form>
        </div>
    </div>

@endsection
