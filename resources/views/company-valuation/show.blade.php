@extends('layouts.master')
@section('page_title', 'Company Valuation')

@section('content')
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Company Valuation</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('company-valuations.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $companyValuation->name }}
                        </div>
                        <div class="form-group">
                            <strong>Status:</strong>
                            {{ $companyValuation->status }}
                        </div>

                    </div>
                </div>

@endsection
