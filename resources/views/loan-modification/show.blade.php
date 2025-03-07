@extends('layouts.master')
@section('page_title', 'Loan Modification')

@section('content')
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Loan Modification</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('loan-modifications.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Loan Id:</strong>
                            {{ $loanModification->loan_id }}
                        </div>
                        <div class="form-group">
                            <strong>Modification:</strong>
                            {{ $loanModification->modification }}
                        </div>
                        <div class="form-group">
                            <strong>Amount:</strong>
                            {{ $loanModification->amount }}
                        </div>
                        <div class="form-group">
                            <strong>Modify By:</strong>
                            {{ $loanModification->modify_by }}
                        </div>

                    </div>
                </div>

@endsection
