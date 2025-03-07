@extends('layouts.master')
@section('page_title', 'Company Valuation')
@section('content')

@section('content')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Company Valuation</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('company-valuations.update', $companyValuation->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('company-valuation.form')

                        </form>
                    </div>
                </div>
@endsection
