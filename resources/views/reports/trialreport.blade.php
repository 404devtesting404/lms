@extends('layouts.master')
@section('page_title', 'Trial Report')
@section('content')
<style>
    tr td ,th{
        padding: 3px !important;

    }
</style>
<div class="card card-default">
    <div class="card-header">
        <span class="card-title">Trial Report</span>
    </div>
    <div class="card-body">
        @if(isset($datef))

        <fieldset>
            <h4>Generated from {{ Qs::dateFormat($datef) }} to {{ Qs::dateFormat($datet) }}</h4>
        </fieldset>
     <div class="table-responsive">
        <table class="table table-bordered datatable-button-html5-columns">
            <thead style="background-color: #97D35D;">
                <tr class="datatable-header center">
                    <th>Code</th>
                    <th>Title</th>
                    <th>Transaction Date</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
            </thead>
            <tbody>

                @foreach($ReportData as $L1_Key=>$L1_ReportRow)
                <tr style="background: #ffff80;font-weight: bold" class="border-blue">
                    <td a>{{ $L1_Key }}</td>
                    <td colspan="2">{{ $L1[$L1_Key]['Title'] }}</td>
                    <td>0</td>
                    <td>{{ number_format($L1[$L1_Key]['Debit']) }}</td>
                    <td>{{ number_format($L1[$L1_Key]['Credit']) }}</td>
                </tr>
                @foreach($L1_ReportRow as $L2_Key=>$L2_ReportRow)
{{--                <tr class="border-blue">--}}
{{--                    <td>{{ $L2_Key }}</td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}

{{--                </tr>--}}

                @foreach($L2_ReportRow as $L3_Key=>$L3_ReportRow)
{{--                <tr class="border-blue">--}}
{{--                    <td>{{ $L3_Key }}</td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                </tr>--}}

                @foreach($L3_ReportRow as $L4_Key=>$L4_ReportRow)
{{--                <tr class="border-blue">--}}
{{--                    <td>{{ $L4_Key }}</td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                </tr>--}}

                @foreach($L4_ReportRow as $L5_Key=>$Row)
                @foreach($Row as $Row)

                <tr class="border-blue">
                    <td>{{ $Row->L5_Code }}</td>
                    <td>{{ $Row->L5_Title }}</td>
                    <td>{{ date("M j, Y", strtotime($Row->txn_date)) }}</td>
                    <td align="right">{{ number_format($Row->debit) }}</td>
                    <td align="right">{{ number_format($Row->credit) }}</td>
                </tr>
                @endforeach
                @endforeach
                @endforeach
                @endforeach
                @endforeach
                @endforeach
            </tbody>
        </table><br><br>
     </div>

        @else

        <form method="POST" action="{{ route('reports.trialreport') }}"  role="form" enctype="multipart/form-data">
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
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
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
