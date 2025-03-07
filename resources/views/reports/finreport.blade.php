@extends('layouts.master')
@section('page_title', 'Financial Report (Test)')
@section('content')

<div class="card card-default">
    <div class="card-header">
        <span class="card-title">Financial Report (Test)</span>
    </div>
    <div class="card-body">
        @if(isset($datef))

        <fieldset>
            <legend>Report Criteria:</legend>
            <h3 class="card-title">Generated from {{ $datef }} to {{ $datet }}</h3>
         </fieldset>


        @foreach($ReportData as $ReportRow)
        <div class="table-responsive">
        <table class="table table-bordered datatable-button-html5-columns">
            <thead style="background-color: #97D35D;">

                <tr class="datatable-header center" style="padding: 4px;">
                    <th width="5%">Sr.#</th>
                    <th width="20%">Code</th>
                    <th width="40%">Descriptions</th>
                    <th width="15%">Transaction</th>
                    <th  style="text-align:right" width="10%">Debit</th>
                    <th style="text-align:right" width="10%">Credit</th>

                </tr>
                <tr class="border-blue">
                    <td style="text-align: center; font-size: 15px; font-weight: bold;background: #ffff80;" colspan="6">{{ $ReportRow[0]->txn_type=="1" ? "BPV" : "CPV" }} {{ $ReportRow[0]->txn_series }}</td>
                </tr>
            </thead>
            <tbody>


                <?php $GrandDebit=0 ?>
                <?php $GrandCredit=0 ?>
                <?php $i=0 ?>
                @foreach($ReportRow as $Row)
                <tr class="border-blue">
                    <td>{{ ++$i }}</td>
                    <td>{{ $Row->code }}</td>
                    <td>{{ $Row->title }}</td>
                    <td>{{ date("M j, Y", strtotime($Row->txn_date)) }}</td>
                    <td align="right">{{ number_format($Row->debit) }}</td>
                    <td align="right">{{ number_format($Row->credit) }}</td>

                </tr>

                <?php $GrandDebit+=$Row->debit ?>
                <?php $GrandCredit+=$Row->credit ?>
                @endforeach

            </tbody>
            <tfoot>

            </tfoot>
            <tr class="border-blue" style="border-color: #97D35D;border-width: 2px 0px 2px 0px;border-style: double;">
                <td colspan="4" class="text-right"><b>Total</b></td>
                <td class="text-right"><b>{{ number_format($GrandDebit) }}</b></td>
                <td class="text-right"><b>{{ number_format($GrandCredit) }}</b></td>
            </tr>
        </table>
        </div>
       <br><br>
        @endforeach


        @else

        <form method="POST" action="{{ route('reports.financialreport') }}"  role="form" enctype="multipart/form-data">
            @csrf

            <div class="box box-info padding-1">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            {{ Form::label('Select Chart of Account:') }}
                            <select name="chartofaccount" id="chartofaccount" class="form-control select-search">
                                <option value="">Select Chart Of Account</option>
                                @foreach($chartOfAccounts as $row)
                                    <option value="{{$row->id}}">{{$row->code}} - {{$row->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            {{ Form::label('Date From:') }}
                            {{ Form::date('datefrom', null, ['class' => 'form-control' . ($errors->has('datefrom') ? ' is-invalid' : ''), 'placeholder' => 'Date From:']) }}
                            {!! $errors->first('due_id', '<p class="invalid-feedback">:message</p>') !!}
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            {{ Form::label('Date To:') }}
                            {{ Form::date('dateto', null, ['class' => 'form-control' . ($errors->has('dateto') ? ' is-invalid' : ''), 'placeholder' => 'Date To:']) }}
                            {!! $errors->first('loan_id', '<p class="invalid-feedback">:message</p>') !!}
                        </div>
                    </div>
                </div>
                <br>
                <div class="box-footer mt20 text-right">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </form>

        @endif
    </div>
</div>

@endsection
