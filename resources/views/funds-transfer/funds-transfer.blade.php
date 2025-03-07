@extends('layouts.master')
@section('page_title', 'Bank Accounts')
@section('content')
    <style>
        .error {
            color: red;
        }
    </style>
    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Fund Transfer</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('transfer.post') }}" role="form" enctype="multipart/form-data">
                @csrf
                @if(session()->has('message'))
                    <div class="alert alert-info">
                        {{ session('message') }}
                    </div>
                @endif
                @if(session()->has('warning'))
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>
                @endif
                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="row form-group">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                <label style="width: 100%;"> Account:</label>
                                {{ Form::select('account[]',
                                    [
                                        '0'=> 'Select type',
                                        '1'=> 'GL',
                                        '2'=> 'Accounts',
                                    ],
                                            null, ['class' => 'report_type form-control account' . ($errors->has('account') ? ' is-invalid' : '')]) }}
                                {!! $errors->first('account', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                <label style="width: 100%;">
                                    Bank Account:
                                    <div class="show-balance float-right"></div>
                                </label>
                                <select class="report_type form-control bank_account {{($errors->has('bank_account') ? ' is-invalid' : '')}}"
                                        name="bank_account[]">

                                </select>
                                {!! $errors->first('bank_account', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                <label style="width: 100%;"> Type:</label>
                                {{ Form::select('type[]',
                                    [
                                        '2'=> 'Select type',
                                        '0'=> 'Credit',
                                        '1'=> 'Debit',
                                    ],
                                            1, ['class' => 'report_type form-control type' . ($errors->has('type') ? ' is-invalid' : '')]) }}
                                {!! $errors->first('type', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                                {{ Form::label('Amount') }}
                                {{ Form::text('amount[]', null, ['class' => 'form-control amount' . ($errors->has('amount') ? ' is-invalid' : ''), 'placeholder' => 'Amount:', 'onkeyup' => 'checkAmount(this)']) }}
                                {!! $errors->first('amount', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                {{ Form::label('Date') }}
                                {{ Form::date('date[]', date('Y-m-d'), ['class' => 'datefrom form-control' . ($errors->has('date') ? ' is-invalid' : ''), 'placeholder' => 'Date:']) }}
                                {!! $errors->first('date', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                <label style="width: 100%;"> Account:</label>
                                {{ Form::select('account[]',
                                    [
                                        '0'=> 'Select type',
                                        '1'=> 'GL',
                                        '2'=> 'Accounts',
                                    ],
                                            null, ['class' => 'report_type form-control account' . ($errors->has('account') ? ' is-invalid' : '')]) }}
                                {!! $errors->first('account', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                <label style="width: 100%;">
                                    Bank Account:
                                    <div class="show-balance float-right"></div>
                                </label>
                                <select class="report_type form-control bank_account {{($errors->has('bank_account') ? ' is-invalid' : '')}}"
                                        name="bank_account[]">

                                </select>
                                {!! $errors->first('bank_account', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                <label style="width: 100%;"> Type:</label>
                                {{ Form::select('type[]',
                                    [
                                        '2'=> 'Select type',
                                        '0'=> 'Credit',
                                        '1'=> 'Debit',
                                    ],
                                            0, ['class' => 'report_type form-control type' . ($errors->has('type') ? ' is-invalid' : '')]) }}
                                {!! $errors->first('type', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                                {{ Form::label('Amount') }}
                                {{ Form::text('amount[]', null, ['class' => 'form-control amount2' . ($errors->has('amount') ? ' is-invalid' : ''), 'placeholder' => 'Amount:', 'onkeyup'=> 'compareAmountToAmount(this)']) }}
                                {!! $errors->first('amount', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                {{ Form::label('Date') }}
                                {{ Form::date('date[]', date('Y-m-d'), ['class' => 'datefrom form-control' . ($errors->has('date') ? ' is-invalid' : ''), 'placeholder' => 'Date:']) }}
                                {!! $errors->first('date', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"><br>
                            <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Report</span>
        </div>
        <div class="card-body">

            <fieldset>
                <legend>Report Criteria:</legend>
                {{--                    <h3 class="card-title">Generated from {{ $datef }} to {{ $datet }}</h3>--}}

            </fieldset>

            <div class="table-responsive">

                <table class="table datatable-button-html5-columns">
                    <thead>

                    <tr>
                        <th>Sr.#</th>
                        <th>Account</th>
                        <th>Bank Account</th>
                        <th>Account Title</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($fund_transfers as $fund_transfer)
                        <tr class="border-blue">
                            <td>{{ $fund_transfer->id }}</td>
                            <td>{{ $fund_transfer->account == 1 ? 'GL' : 'Accounts' }}</td>
                            <td>{{ $fund_transfer->bank_account }}</td>
                            <td>{{ $fund_transfer->account == 2 ? \App\Helpers\Qs::getUserInfo($fund_transfer->bank_account) : ($fund_transfer->bank_account == 390000000269 ? 'Asaan Ghar Finance Limited' : 'Asaan Ghar Finance Limited - Collection') }}</td>
                            <td>{{ $fund_transfer->type == 0 ? 'Credit' : 'Debit' }}</td>
                            <td>{{ date('d-M-Y', strtotime($fund_transfer->date)) }}</td>
                            <td>{{ number_format($fund_transfer->amount) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(document).on('change', '.account', function () {
            var account = $(this).val();
            var $formGroup = $(this).closest('.form-group');
            var bankAccountSelect = $formGroup.find('.bank_account');
            if (account == 1) {
                var html = '<option value="0" disabled selected>Select Account</option>' +
                    '<option value="390000000269">390000000269 Asaan Ghar Finance Limited</option>' +
                    '<option value="390000000436">390000000436 Asaan Ghar Finance Limited - Collection</option>';
                bankAccountSelect.html(html);
                $formGroup.find('.show-balance').html('<span class="float-right balance">Balance: PKR 0</span>');
            } else {
                lms_user(bankAccountSelect);
                $formGroup.find('.show-balance').html('');
            }
        });

        function lms_user(append_div) {
            var payload = {
                "_token": '{{ csrf_token() }}'
            }
            $.post("{{ route('loan.users') }}", payload, function (response) {
//            alert(response);
                if (response.success == true) {
                    append_div.html(response.data);
                }
            });
        }

        $(document).on('change', '.bank_account', function () {
            var bankAccount = $(this).val();
            var $formGroup = $(this).closest('.form-group');
            var $balance = $formGroup.find('.balance');

            bankDetails(bankAccount, $balance);
        });

        function bankDetails(account, $balance) {
            var payload = {
                bank_account: account,
                "_token": '{{ csrf_token() }}'
            };

            $.post("{{ route('bank.details') }}", payload, function (response) {
                if (response.success == true) {
                    $balance.empty().text('Balance: PKR ' + response.data);
                } else {
                    alert("Unable to update because case is already disbursed");
                }
            });
        }

        function checkAmount(input) {
            // Get the value of the amount input
            var amount = parseFloat(input.value);

            // Find the closest form-group and then find the balance element within it
            var $formGroup = $(input).closest('.form-group');
            var balanceText = $formGroup.find('.balance').text();

            // Extract the numeric value from the balance text
            var balance = parseFloat(balanceText.replace(/[^0-9.-]+/g, ""));

            // Compare the amount with the balance
            if (amount > balance) {
                if ($formGroup.find('.error').length === 0) {
                    // Add error message right after the amount input
                    $(input).after('<div class="error">Insufficient Balance</div>');
                    $('.btn-submit').prop('disabled', true);
                }
            } else {
                $formGroup.find('.error').remove();
                $('.btn-submit').prop('disabled', false);
            }
        }

        function compareAmountToAmount(input) {
            var amount1 = parseFloat(input.value);
            var amount2 = parseFloat($('.amount').val());
            var $formGroup = $(input).closest('.form-group');
            if (amount1 !== amount2) {
                if ($formGroup.find('.error').length === 0) {
                    $('.amount2').after('<div class="error">Amount should be equal</div>');
                }
                $('.btn-submit').prop('disabled', true);
            } else {
                $formGroup.find('.error').remove();
                $('.btn-submit').prop('disabled', false);
            }
        }

    </script>
@endsection
