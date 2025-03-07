@extends('layouts.master')
@section('page_title', 'SCRS Report')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">SCRS Report</span>
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
                        <th>Type of the borrower (Individual/ AOP/ Company)</th>
                        <th>Borrower / Party Name</th>
                        <th>Group Name / Co- Applicant</th>
                        <th>Type of the facility</th>
                        <th>Associated (Yes / No)</th>
                        <th>Date of Transaction (DD/MM/YYYY)</th>
                        <th>Date of Maturity (DD/MM/YYYY)</th>
                        <th>Rate of Return / Markup (%)</th>
                        <th>Amount sanctioned (in millions)</th>
                        <th>Amount Disbursed (in millions)</th>
                        <th>Principal outstanding (in millions)</th>
                        <th>Profit Outstanding (in millions)</th>
                        <th>Detail of Security</th>
                        <th>Last valuation date (DD/MM/YYYY)</th>
                        <th> Value of security (in millions)</th>
                        <th>% of NBFC's equity (%) (Principal / Equity)</th>
                        <th>Income suspended (in millions)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ReportData as $ReportRow)
                        <tr class="border-blue">
                            <td>Individual</td>
                            <td>{{ $ReportRow['name'] }}</td>
                            <td>-</td>
                            <td>{{ $ReportRow['product_name'] }}</td>
                            <td>Yes</td>
                            <td>{{ date('d M Y', strtotime($ReportRow['disb_date'])) }}</td>
                            <td>{{ date('d M Y', strtotime($ReportRow['maturity_date'])) }}</td>
                            <td>{{ $ReportRow['ror'] }}%</td>
                            <td><?php
                                    $amount_sanctioned = isset($mod_amount[$ReportRow['loan_id']]) ?
                                        ($mod_amount[$ReportRow['loan_id']]+$ReportRow['amount_sanctioned']) :
                                        $ReportRow['amount_sanctioned']

                                    ?>
                                {{ number_format($amount_sanctioned/1000000, 2) }}
                            </td>
                            <td><?php
                                    $amount_sanctioned = isset($mod_amount[$ReportRow['loan_id']]) ?
                                        ($mod_amount[$ReportRow['loan_id']]+$ReportRow['amount_sanctioned']) :
                                        $ReportRow['amount_sanctioned']

                                    ?>
                                {{ number_format($amount_sanctioned/1000000, 2) }}
                            </td>
                            <td>
                                <?php
                                    $outstanding = isset($mod_amount[$ReportRow['loan_id']]) ?
                                        ($mod_amount[$ReportRow['loan_id']]+$ReportRow['outs']) :
                                        ( $ReportRow['outs'] ? $ReportRow['outs'] : $ReportRow['finance_amount'] )

                                ?>
                                {{ number_format($outstanding/1000000, 2) }}
                            </td>
                            <td>{{ number_format($ReportRow['profit']/1000000, 2) }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
