@extends('layouts.master')
@section('page_title', 'Company Income Est')

@section('content')
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Company Income Est</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('company-income-ests.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $companyIncomeEst->name }}
                        </div>
                        <div class="form-group">
                            <strong>Status:</strong>
                            {{ $companyIncomeEst->status }}
                        </div>

                    </div>
                </div>

@endsection
