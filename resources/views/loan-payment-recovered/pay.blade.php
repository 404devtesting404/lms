@extends('layouts.master')
@section('page_title', 'Financing Payment Recovered')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Financing Payment Recovered</span>
        </div>
        <div class="card-body">
            <form method="POST" onsubmit="submitform" action="{{ route('loan-payment-recovereds.storepay') }}"  role="form" enctype="multipart/form-data">
                @csrf

<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group ">
            <span><strong>Note:</strong> Total due amount is : <strong>{{ number_format($TotalAmountDue,0) }}</strong> till today.<br>Single Installment Due amount is: {{ number_format($AmountInstallment,0) }} </span>
            
            {{ Form::hidden('loan_id',$loanId, $loanPaymentRecovered->loan_id) }}
        </div>
        <div class="form-group">
            {{ Form::label('Collection Type') }}
            {{ Form::select('payment_type', ['Due/Advance Payment','Baloon Payment'], '', ['class' => 'form-control  select-search']) }}
        </div>
        <div class="form-group">
            {{ Form::label('Collected Amount') }}
            {{ Form::text('amount_total', $loanPaymentRecovered->amount_total, ['class' => 'form-control' . ($errors->has('amount_total') ? ' is-invalid' : ''), 'placeholder' => 'Amount Total']) }}
            {!! $errors->first('amount_total', '<div class="invalid-feedback">:message</p>') !!}
        </div>
        <div class="form-group">
            {{ Form::hidden('recovered_by',auth()->user()->id, $loanPaymentRecovered->recovered_by) }}
        </div>
        <div class="form-group">
            {{ Form::label('Posting Date') }}
            {{ Form::date('recovered_date', $loanPaymentRecovered->recovered_date, ['class' => 'form-control' . ($errors->has('recovered_date') ? ' is-invalid' : ''), 'placeholder' => 'Recovered Date']) }}
            {!! $errors->first('recovered_date', '<div class="invalid-feedback">:message</p>') !!}
        </div>

            <input type="hidden" id="mindue" name="minduedate" value="{{ $MinDueDate }}" />
    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
            </form>
        </div>
    </div>

@endsection
