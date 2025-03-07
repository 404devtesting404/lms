@extends('layouts.master')
@section('page_title', 'Takaful Report')
@section('content')

<div class="card card-default">
    <div class="card-header">
        <span class="card-title">Takaful Report</span>
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
                        <th>Contact Number</th>
                        <th>Customer Name</th>
                        <th>CNIC</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Booking Date</th>
                        <th>Outstanding Amount</th>
                        <th>Takaful Number</th>
                        <th>Takaful Start Date</th>
                        <th>Takaful End Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ReportData as $ReportRow)
                    <tr  class="border-blue">
                        <td>{{ ++$i }}</td>
                        <td>{{ $ReportRow->account_no }}</td>
                        <td>{{ $ReportRow->name }}</td>
                        <td>{{ $ReportRow->cnic }}</td>
                        <td>{{ $ReportRow->address }}</td>
                        <td>{{ $ReportRow->city }}</td>
                        <td>{{ date("d M Y", strtotime($ReportRow->disb_date)) }}</td>
                        <td>{{ 
                            isset($mod_amount[$ReportRow->loan_id]) ? number_format(($mod_amount[$ReportRow->loan_id]+$ReportRow->outs),0) : ( $ReportRow->outs ? number_format($ReportRow->outs,0) : number_format($ReportRow->finance_amount) ) }}</td>

                        <td>{{ $ReportRow->policy_number }}</td>
                        <td>{{ $ReportRow->start_date }}</td>
                        <td>{{ $ReportRow->end_date }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
