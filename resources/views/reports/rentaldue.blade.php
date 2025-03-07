@extends('layouts.master')
@section('page_title', 'Outstanding Portfolio Report')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Outstanding Portfolio Report</span>
        </div>
        <div class="card-body">

            <fieldset>
                <legend>Report Criteria:</legend>
                <h3 class="card-title">Generated from {{ $datef }} to {{ $datet }}</h3>

            </fieldset>

            <div class="table-responsive">

                <table class="table datatable-button-html5-columns">
                    <thead>
                        <tr>
                            <th>Sr.#</th>
                            <th>Borrower Name</th>
                            <th>CNIC</th>
                            <th>Booking Date</th>
                            <th>AGFL Finance Amount</th>
                            <th>Outstanding Amount</th>
                            {{--                        <th>Principle Amount Rec.</th>--}}
                            {{--                        <th>Profit Amount Rec.</th>--}}
                            <th>Rentals Amount</th>
                            <th>Cycle Date</th>
                            <th>Due Date</th>
                            <th>No. Of Inst. Rec.</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($ReportData as $ReportRow)
                        @php
                            $inst_no = $ReportRow['inst_no'] ? $ReportRow['inst_no'] : 0;
                        @endphp
                        <tr class="border-blue">
                            <td>{{ ++$i }}</td>
                            <td>{{ $ReportRow['name'] }}</td>
                            <td>{{ $ReportRow['cnic'] }}</td>
                            <td>{{ date("d M Y", strtotime($ReportRow['disb_date'])) }}</td>
                            <td>{{ isset($mod_amount[$ReportRow['loan_id']]) ?
                                    number_format(($mod_amount[$ReportRow['loan_id']]+$ReportRow['finance_amount']),0)
                                    : number_format($ReportRow['finance_amount'],0) }}
                            </td>
                            <td>{{
                            isset($mod_amount[$ReportRow['loan_id']]) ?
                            number_format(($mod_amount[$ReportRow['loan_id']]+$ReportRow['outs']),0) :
                            ( $ReportRow['outs'] ? number_format($ReportRow['outs'],0) : number_format($ReportRow['finance_amount']) )}}
                            </td>

                            {{--                        <td>{{ number_format($ReportRow->amount_pr) }}</td>--}}
                            {{--                        <td>{{ number_format($ReportRow->amount_mu) }}</td>--}}

                            <td>{{ number_format($due_detail[$ReportRow['loan_id']]->amount_total) }}</td>

                            <td>{{ date("d",strtotime($due_detail[$ReportRow['loan_id']]->due_date)) }}</td>
                            <td>{{ date("d M Y",strtotime($due_detail[$ReportRow['loan_id']]->due_date)) }}</td>
                            <td>{{ $inst_no }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
