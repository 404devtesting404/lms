@extends('layouts.master')
@section('page_title', 'Financing Payment Recovered')
@section('content')

    <?php
        $Profit = !empty($loan_recovered) ? $loan_recovered->locked_profit : $Profit;
    ?>
    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Financing Payment Recovered</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('loan-payment-recovereds.earlypay') }}" role="form"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="profit" value="{{ !empty($loan_recovered) ? $loan_recovered->locked_profit : $Profit }}">

                <div class="box box-info padding-1">
                    <div class="box-body">

                        <div class="form-group ">
                        <span>
                            Name: <strong>{{ $OutstandingData->loan_history->loan_borrower->fname }} {{ $OutstandingData->loan_history->loan_borrower->mname }} {{ $OutstandingData->loan_history->loan_borrower->lname }}</strong><br>
                            Outstanding Amount: <strong>{{ number_format($outstanding,0) }}</strong> <br>
                            Profit for {{ $days_diff }} days: <strong>{{ number_format($Profit,0) }} </strong><br>
                            Early Settlement Charges: <strong>{{ number_format($SettlementCharges,0) }} </strong><br>
                            FED on Settlement Charges: <strong>{{ number_format($FED,0) }} </strong><br>
                            Charity Amount: <strong>{{ number_format($TotalCharity,0) }} </strong><br>
                            Total Settlement Amount: <strong>{{ number_format($TotalSettlement,0) }} </strong><br>
                            @if(!empty($loan_recovered))
                            Settle Date: <strong>{{ date('d-M-Y', strtotime($loan_recovered->recovered_date)) }} </strong><br>
                            @endif

                        </span>
                            <input type="hidden" id="loanId" name="loanId" value="{{ $loanId }}"/>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Initial Settlement Amount</h3>
                                <div class="form-group">
                                    {{ Form::label('Outstanding Amount') }}
                                    {{ Form::text('', $outstanding, ['class' => 'form-control' . ($errors->has('amount_total') ? ' is-invalid' : ''), 'readonly' ,'placeholder' => 'Amount Total']) }}
                                    {!! $errors->first('amount_total', '<div class="invalid-feedback">:message</p>') !!}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('Profit for '. $days_diff .' days') }}
                                    {{ Form::text('', $Profit, ['class' => 'form-control' . ($errors->has('amount_profit') ? ' is-invalid' : ''), 'readonly' ,'placeholder' => 'Amount Profit']) }}
                                    {!! $errors->first('amount_profit', '<div class="invalid-feedback">:message</p>') !!}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('Settlement Charges') }}
                                    {{ Form::text('', $SettlementCharges, ['class' => 'form-control' . ($errors->has('amount_settlement') ? ' is-invalid' : ''), 'readonly' ,'placeholder' => 'Amount Settlement']) }}
                                    {!! $errors->first('amount_settlement', '<div class="invalid-feedback">:message</p>') !!}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('FED Amount') }}
                                    {{ Form::text('', $FED, ['class' => 'form-control' . ($errors->has('amount_fed') ? ' is-invalid' : ''), 'readonly' ,'placeholder' => 'Amount FED']) }}
                                    {!! $errors->first('amount_fed', '<div class="invalid-feedback">:message</p>') !!}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('Charity Amount') }}
                                    {{ Form::text('', $TotalCharity, ['class' => 'form-control' . ($errors->has('amount_charity') ? ' is-invalid' : ''), 'readonly' ,'placeholder' => 'Charity Amount']) }}
                                    {!! $errors->first('amount_charity', '<div class="invalid-feedback">:message</p>') !!}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('Total Settlement Amount') }}
                                    {{ Form::text('', $TotalSettlement, ['class' => 'form-control' . ($errors->has('amount_total') ? ' is-invalid' : ''), 'readonly' ,'placeholder' => 'Amount Total']) }}
                                    {!! $errors->first('amount_total', '<div class="invalid-feedback">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3>Settlement Amount</h3>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{ Form::label('Outstanding Amount') }}
                                            {{ Form::text('amount_outstanding', $outstanding, ['class' => 'form-control' . ($errors->has('amount_total') ? ' is-invalid' : ''), 'readonly' ,'placeholder' => 'Amount Total', 'id' => 'amount_outstanding']) }}
                                            {!! $errors->first('amount_total', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('Profit for '. $days_diff .' days') }}
                                            {{ Form::text('amount_profit', !empty($loan_recovered) ? number_format($loan_recovered->amount_mu, 0) : 0, ['class' => 'form-control' . ($errors->has('amount_profit') ? ' is-invalid' : ''), 'placeholder' => 'Amount Profit', 'id' => 'amount_profit', 'onkeyup'=> 'calculateWaivedPercentage('.$Profit.',this.value,"amount_profit_percentage")']) }}
                                            {!! $errors->first('amount_profit', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('In %') }}
                                            {{ Form::text('', '', ['class' => 'form-control calculate-percentage' . ($errors->has('amount_profit') ? ' is-invalid' : ''), 'id'=> 'amount_profit_percentage','readonly', 'data-value'=> $Profit, 'data-waived_value'=> !empty($loan_recovered) ? $loan_recovered->amount_mu : 0 ]) }}
                                            {!! $errors->first('amount_profit', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('Settlement Charges') }}
                                            {{ Form::text('amount_settlement', !empty($loan_recovered) ? $loan_recovered->amount_settlement : 0, ['class' => 'form-control' . ($errors->has('amount_settlement') ? ' is-invalid' : ''), 'placeholder' => 'Amount Settlement', 'id' => 'amount_settlement', 'onkeyup'=> 'calculateWaivedPercentage('.$SettlementCharges.',this.value, "amount_settlement_percentage")']) }}
                                            {!! $errors->first('amount_settlement', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('In %') }}
                                            {{ Form::text('', '', ['class' => 'form-control calculate-percentage' . ($errors->has('amount_profit') ? ' is-invalid' : ''), 'id'=> 'amount_settlement_percentage','readonly', 'data-value'=> $SettlementCharges, 'data-waived_value'=> !empty($loan_recovered) ? $loan_recovered->amount_settlement : 0 ]) }}
                                            {!! $errors->first('amount_profit', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('FED Amount') }}
                                            {{ Form::text('amount_fed', !empty($loan_recovered) ? $loan_recovered->amount_fed : 0, ['class' => 'form-control' . ($errors->has('amount_fed') ? ' is-invalid' : ''), 'placeholder' => 'Amount FED', 'id' => 'amount_fed', 'onkeyup'=> 'calculateWaivedPercentage('.$FED.',this.value, "amount_fed_percentage")']) }}
                                            {!! $errors->first('amount_fed', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('In %') }}
                                            {{ Form::text('', '', ['class' => 'form-control calculate-percentage' . ($errors->has('amount_profit') ? ' is-invalid' : ''), 'id'=> 'amount_fed_percentage','readonly', 'data-value'=> $FED, 'data-waived_value'=> !empty($loan_recovered) ? $loan_recovered->amount_fed : 0 ]) }}
                                            {!! $errors->first('amount_profit', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('Charity Amount') }}
                                            {{ Form::text('amount_charity', !empty($loan_recovered) ? $loan_recovered->amount_charity : 0, ['class' => 'form-control' . ($errors->has('amount_charity') ? ' is-invalid' : ''), 'placeholder' => 'Charity Amount', 'id' => 'amount_charity', 'onkeyup'=> 'calculateWaivedPercentage('.$TotalCharity.',this.value, "amount_charity_percentage")']) }}
                                            {!! $errors->first('amount_charity', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('In %') }}
                                            {{ Form::text('', '', ['class' => 'form-control calculate-percentage' . ($errors->has('amount_profit') ? ' is-invalid' : ''), 'id'=> 'amount_charity_percentage','readonly', 'data-value'=> $TotalCharity, 'data-waived_value'=> !empty($loan_recovered) ? $loan_recovered->amount_charity : 0 ]) }}
                                            {!! $errors->first('amount_profit', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{ Form::label('Total Settlement Amount') }}
                                            {{ Form::text('amount_total', !empty($loan_recovered) ? number_format($loan_recovered->amount_total, 0) : 0, ['class' => 'form-control' . ($errors->has('amount_total') ? ' is-invalid' : ''), 'readonly' ,'placeholder' => 'Amount Total', 'id' => 'amount_total']) }}
                                            {!! $errors->first('amount_total', '<div class="invalid-feedback">:message</p>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer mt20">
                        <button type="submit" class="btn btn-primary" {{ !empty($loan_recovered) ? 'disabled' : '' }}>
                            Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $('.calculate-percentage').each(function() {
            var value = $(this).attr('data-value');
            var waived_value = $(this).attr('data-waived_value');
            console.log(value, waived_value);
            var percentageWaived = parseInt(waived_value) / value * 100;
            var percentage = parseFloat(percentageWaived).toFixed(2);
            $(this).val(!isNaN(percentage) ? percentage : 0);
        });


        function safeParseInt(value) {
            return isNaN(parseInt(value)) ? 0 : parseInt(value);
        }

        function calculateWaivedPercentage(initialProfit, waivedAmount, append_id) {
            var percentageWaived = parseInt(waivedAmount) / initialProfit * 100;
            var percentage = parseFloat(percentageWaived).toFixed(2)
            $('#'+append_id).val(!isNaN(percentage) ? percentage : 0);
        }

        $('#amount_profit, #amount_settlement, #amount_fed, #amount_charity, #amount_total').on('keyup', function () {
            var amount_outstanding = safeParseInt($('#amount_outstanding').val());
            var amount_profit = safeParseInt($('#amount_profit').val());
            var amount_settlement = safeParseInt($('#amount_settlement').val());
            var amount_fed = safeParseInt($('#amount_fed').val());
            var amount_charity = safeParseInt($('#amount_charity').val());

            var total_amount = amount_outstanding + amount_profit + amount_settlement + amount_fed + amount_charity;

            $('#amount_total').val(total_amount);
        });

        // $('.calculate-percentage').on('keyup', function (){
        //     var value = $(this).val();
        //     calculateWaivedPercentage();
        // })
    </script>
@endsection


