@extends('layouts.master')
@section('page_title', 'Company Legal')

@section('content')
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Company Legal</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('company-legals.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $companyLegal->name }}
                        </div>
                        <div class="form-group">
                            <strong>Status:</strong>
                            {{ $companyLegal->status }}
                        </div>

                    </div>
                </div>

@endsection
