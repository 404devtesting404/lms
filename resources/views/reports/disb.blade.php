@extends('layouts.master')
@section('page_title', 'Disbursement Report')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Disbursement Report</span>
        </div>
        <div class="card-body">
            @if(isset($datef))

                <fieldset>
                    <legend>Report Criteria:</legend>
                    <h3 class="card-title">Generated from {{ $datef }} to {{ $datet }}</h3>

                </fieldset>

                <div class="table-responsive">

                    <table class="table datatable-button-html5-columns">
                        <thead>

                        <tr>
                            <th>Sr.#</th>
                            <th>Booking Date</th>
                            <th>Borrower Name</th>
                            <th>CNIC</th>
                            <th>Account#</th>
                            <th>Product Name</th>
                            <th>Maturity Date</th>
                            <th>Finance Amount</th>
                            <th>Status</th>
                            <th>Tenure</th>
                            <th>Kibor Rate</th>
                            <th>Spread Rate</th>
                            <th>Total Rate</th>
                            <th>Rental Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ReportData as $ReportRow)
                            <tr class="border-blue">
                                <td>{{ ++$i }}</td>
                                <td>{{ date("d M Y", strtotime($ReportRow->disb_date)) }}</td>
                                <td>{{ $ReportRow->name }}</td>
                                <td>{{ $ReportRow->cnic }}</td>
                                <td>{{ $ReportRow->account_no }}</td>
                                <td>{{ $ReportRow->loan_type }}</td>
                                <td>{{ date("d M Y", strtotime($ReportRow->maturity_date)) }}</td>
                                <td>{{
//                            isset($mod_amount[$ReportRow->loan_id]) ?
//                             number_format(($mod_amount[$ReportRow->loan_id]+$ReportRow->finance_amount),0) :
//                             number_format($ReportRow->finance_amount,0)
                                number_format($ReportRow->finance_amount,0)
                         }}
                                </td>
                                <td>
                                    {{ $ReportRow->status }}
                                </td>
                                <td>{{ $ReportRow->loan_period/12 }} Years</td>
                                <td>{{ $ReportRow->kibor_rate."%" }}</td>
                                <td>{{ $ReportRow->spread_rate."%" }}</td>
                                <td>{{ ($ReportRow->kibor_rate+$ReportRow->spread_rate)."%" }}</td>
                                <td>{{ number_format($ReportRow->amount_total,0) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            @else

                <form method="POST" action="{{ route('reports.disbreport') }}" role="form"
                      enctype="multipart/form-data">
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
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"><br>
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
