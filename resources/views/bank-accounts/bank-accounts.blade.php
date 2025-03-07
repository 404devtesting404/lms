@extends('layouts.master')
@section('page_title', 'Bank Accounts')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Bank Accounts</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('bank.post') }}" role="form" enctype="multipart/form-data">
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
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                <label style="width: 100%;"> Bank Account: <span class="float-right" id="balance">Balance: PKR {{ $balance }}</span></label>
                                {{ Form::select('bank_account',
                                    [
                                        '0'=> 'Select Bank Accounts',
                                        '390000000269'=> '390000000269 Asaan Ghar Finance Limited',
                                        '390000000436'=> '390000000436 Asaan Ghar Finance Limited - Collection',
                                    ],
                                            null, ['class' => 'report_type form-control bank_account' . ($errors->has('bank_account') ? ' is-invalid' : '')]) }}
                                {!! $errors->first('bank_account', '<p class="invalid-feedback">:message</p>') !!}
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                {{ Form::label('Date') }}
                                {{ Form::date('date', date('Y-m-d'), ['class' => 'datefrom form-control' . ($errors->has('date') ? ' is-invalid' : ''), 'placeholder' => 'Date:']) }}
                                {!! $errors->first('date', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                <label style="width: 100%;"> Type:</label>
                                {{ Form::select('type',
                                    [
                                        '2'=> 'Select type',
                                        '0'=> 'Credit',
                                        '1'=> 'Debit',
                                    ],
                                            null, ['class' => 'report_type form-control' . ($errors->has('type') ? ' is-invalid' : '')]) }}
                                {!! $errors->first('type', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                {{ Form::label('Amount') }}
                                {{ Form::text('amount', null, ['class' => 'datefrom form-control' . ($errors->has('amount') ? ' is-invalid' : ''), 'placeholder' => 'Amount:']) }}
                                {!! $errors->first('amount', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"><br>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
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
                            <th>Bank Account</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bank_accounts as $bank_account)
                            <tr class="border-blue">
                                <td>{{ $bank_account->id }}</td>
                                <td>{{ $bank_account->account }}</td>
                                <td>{{ date('d-M-Y', strtotime($bank_account->date)) }}</td>
                                <td>{{ $bank_account->type == 0 ? 'Credit' : 'Debit' }}</td>
                                <td>{{  number_format($bank_account->amount) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.bank_account').change(function() {
                var bankAccount = $(this).val();
                bankDetails(bankAccount);
            });
        });

        function bankDetails(account){
            var payload = {
                bank_account: account,
                "_token": '{{ csrf_token() }}'
            }
            $.post("{{ route('bank.details') }}", payload, function (response) {
//            alert(response);
                if (response.success == true) {
                    $('#balance').empty().text('Balance: PKR '+response.data);
                } else {
                    alert("Unable to update because case is already disbursed");
                }
            });
        }
    </script>
@endsection
