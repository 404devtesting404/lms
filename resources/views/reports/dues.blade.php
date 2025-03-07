@extends('layouts.master')
@section('page_title', 'Due Report')
@section('content')

<div class="card card-default">
    <div class="card-header">
        <span class="card-title">Due Report</span>
    </div>
    <div class="card-body">
        @if(isset($datef))

        <fieldset>
            <legend>Report Criteria:</legend>
            <h3 class="card-title">Generated from {{ $datef }} to {{ $datet }}</h3>

        </fieldset>


        <div class="table-responsive">

            <table class="table datatable-button-html5-columns">            <thead style="background-color: #26a69a; color: #FFF;">

                <tr class="datatable-header center"  >
                    <th rowspan="2">Sr.#</th>
                    <th rowspan="2">Borrower Name</th>
                    <th rowspan="2">Borrower CNIC</th>
                    <th rowspan="2">Location</th>
                    <th rowspan="2">A/C#</th>
                    <th rowspan="2">Installment No.</th>
                    <th rowspan="2">Due Date</th>
                    <th colspan="3" style="text-align: center; border-width: 0px 1px 0px 1px;">Current Due</th>

<!--                    <th rowspan="2">Last Recovery Date</th>-->
                </tr>
                <tr class="datatable-header" style="text-align: center; ">
                    <th style="border-width: 1px 0px 0px 1px; border-style: solid;">Principle</th>
                    <th style="border-width: 1px 1px 0px 0px; border-style: solid;">Profit</th>
                    <th style="border-width: 1px 1px 0px 0px; border-style: solid;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ReportData as $ReportRow)
                <tr  class="border-blue">
                    <td>{{ ++$i }}</td>
                    <td>{{ $ReportRow->b_name }}</td>
                    <td>{{ $ReportRow->cnic }}</td>
                    <td>{{ $ReportRow->name }}</td>
                    <td>{{ $ReportRow->id }}</td>
                    <td>{{ $ReportRow->installment_no }}</td>
                    <td>{{ date("j M Y", strtotime($ReportRow->due_date)) }}</td>
                    <td>{{ number_format($ReportRow->am_pr) }}</td>
                    <td>{{ number_format($ReportRow->am_mu) }}</td>
                    <td>{{ number_format(($ReportRow->am_mu+$ReportRow->am_pr)) }}</td>

<!--                    <td>{{ $ReportRow->b_name }}</td>-->

                </tr>
                @endforeach
            </tbody>
        </table>
        </div>


        @else

        <form method="POST" action="{{ route('reports.duesreport') }}"  role="form" enctype="multipart/form-data">
            @csrf
            <div class="box box-info padding-1">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            {{ Form::label('Date From:') }}
                            {{ Form::date('datefrom', null, ['class' => 'form-control' . ($errors->has('datefrom') ? ' is-invalid' : ''), 'placeholder' => 'Date From:']) }}
                            {!! $errors->first('due_id', '<p class="invalid-feedback">:message</p>') !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            {{ Form::label('Date To:') }}
                            {{ Form::date('dateto', null, ['class' => 'form-control' . ($errors->has('dateto') ? ' is-invalid' : ''), 'placeholder' => 'Date To:']) }}
                            {!! $errors->first('loan_id', '<p class="invalid-feedback">:message</p>') !!}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"> <br>
                            <button type="submit" class="btn btn-primary">Generate</button>
                        </div>
                    </div>

            </div>
            </div>
        </form>


        @endif
    </div>
</div>

@endsection
