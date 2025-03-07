@extends('layouts.master')
@section('page_title', 'GL Balance Report')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">GL Balance Report</span>
        </div>
        <div class="card-body">
            @if(isset($datef))
                @foreach($ReportData as $ReportRow)
                    @php $total_amount += ( $chartofaccount=="1" ? $ReportRow->c_am_pr : $ReportRow->c_am_mu) @endphp
                @endforeach
                <fieldset>
                    <legend>Report Criteria:</legend>
                    <h3 class="card-title">
                        {{ $chartofaccount=="1" ? "Investment GL" : "Profit GL" }} Report from {{ $datef }} to {{ $datet }}
                    <span style="float: right">Total Amount: {{ number_format($total_amount,0) }}</span>
                    </h3>

                </fieldset>
                <div class="table-responsive">
                    <table class="table datatable-button-html5-columns">
                        <thead style="background-color: #26a69a; color: #FFF;">
                            <tr class="datatable-header center">
                                <th>Sr.No</th>
                                <th>Customer Name</th>
                                <th>Customer CNIC</th>
                                <th>A/C No</th>
                                <th>Installment No</th>
                                <th>Due Date</th>
                                <th>Payment Date</th>
                                <th>{{ $chartofaccount=="1" ? "Investment GL" : "Profit GL" }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $grand = 0 @endphp
                        @foreach($ReportData as $ReportRow)
                            <tr class="border-blue">
                                <td>{{ ++$i }}</td>
                                <td>{{ $ReportRow->b_name }}</td>
                                <td>{{ $ReportRow->cnic }}</td>
                                <td>{{ $ReportRow->id }}</td>
                                <td>{{ $ReportRow->installment_no }}</td>

                                <td>{{ date("j M Y", strtotime($ReportRow->due_date)) }}</td>
                                <td>{{ date("j M Y", strtotime($ReportRow->recovered_date)) }}</td>
                                <td align="right">{{ $chartofaccount=="1" ? number_format($ReportRow->c_am_pr) : number_format($ReportRow->c_am_mu) }}</td>

                            </tr>
                            @php $grand += ( $chartofaccount=="1" ? $ReportRow->c_am_pr : $ReportRow->c_am_mu) @endphp
                        @endforeach

                        </tbody>

                    </table>
                </div>

            @else

                <form method="POST" action="{{ route('reports.trialreportaccdetail') }}" role="form"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="box box-info padding-1">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    {{ Form::label('Select Chart of Account:') }}
                                    <select name="chartofaccount" id="chartofaccount"
                                            class="form-control select-search">
                                        <option value="">Select Chart Of Account</option>
                                        <option value="1">Investment GL</option>
                                        <option value="2">Profit GL</option>
                                    </select>
                                </div>
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
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                    <br>
                                    <button type="submit" class="btn btn-primary">Generate</button>
                                </div>
                            </div>
                            <div class="form-group">
                            </div>
                            <div class="form-group">
                            </div>

                        </div>
                        <div class="box-footer mt20">

                        </div>
                    </div>
                </form>

            @endif
        </div>
    </div>

@endsection
