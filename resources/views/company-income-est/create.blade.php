@extends('layouts.master')
@section('page_title', 'Company Income Est')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Company Income Est</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('company-income-ests.store') }}"  role="form" enctype="multipart/form-data">
                @csrf

                @include('company-income-est.form')

            </form>
        </div>
    </div>

@endsection
