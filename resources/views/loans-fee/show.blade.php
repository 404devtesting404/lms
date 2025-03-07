@extends('layouts.master')
@section('page_title', 'Loans Fee')

@section('content')
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Loans Fee</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('loans-fees.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Loanid:</strong>
                            {{ $loansFee->loanId }}
                        </div>
                        <div class="form-group">
                            <strong>Processingfees:</strong>
                            {{ $loansFee->processingFees }}
                        </div>
                        <div class="form-group">
                            <strong>Processingfeesstatus:</strong>
                            {{ $loansFee->processingFeesStatus }}
                        </div>
                        <div class="form-group">
                            <strong>Fedfees:</strong>
                            {{ $loansFee->fedFees }}
                        </div>
                        <div class="form-group">
                            <strong>Fedfeesstatus:</strong>
                            {{ $loansFee->fedFeesStatus }}
                        </div>
                        <div class="form-group">
                            <strong>Legalfeescompanyid:</strong>
                            {{ $loansFee->legalFeesCompanyId }}
                        </div>
                        <div class="form-group">
                            <strong>Legalfees:</strong>
                            {{ $loansFee->legalFees }}
                        </div>
                        <div class="form-group">
                            <strong>Legalfeesstatus:</strong>
                            {{ $loansFee->legalFeesStatus }}
                        </div>
                        <div class="form-group">
                            <strong>Valuationcompanyid:</strong>
                            {{ $loansFee->valuationCompanyId }}
                        </div>
                        <div class="form-group">
                            <strong>Valuationfees:</strong>
                            {{ $loansFee->valuationFees }}
                        </div>
                        <div class="form-group">
                            <strong>Valuationfeesstatus:</strong>
                            {{ $loansFee->valuationFeesStatus }}
                        </div>
                        <div class="form-group">
                            <strong>Incomeestcompanyid:</strong>
                            {{ $loansFee->incomeEstCompanyId }}
                        </div>
                        <div class="form-group">
                            <strong>Incomeestfees:</strong>
                            {{ $loansFee->incomeEstFees }}
                        </div>
                        <div class="form-group">
                            <strong>Incomeestfeesstatus:</strong>
                            {{ $loansFee->incomeEstFeesStatus }}
                        </div>
                        <div class="form-group">
                            <strong>Stamppaperfees:</strong>
                            {{ $loansFee->stampPaperFees }}
                        </div>

                    </div>
                </div>

@endsection
