@extends('layouts.master')
@section('page_title', 'Company Valuation')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Company Valuation</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('company-valuations.store') }}"  role="form" enctype="multipart/form-data">
                @csrf

                @include('company-valuation.form')

            </form>
        </div>
    </div>

@endsection
