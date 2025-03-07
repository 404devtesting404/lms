@extends('layouts.master')
@section('page_title', 'AGF Portfolio Report')
@section('content')

<div class="card card-default">
    <div class="card-header">
        <span class="card-title">AGF Portfolio Report</span>
    </div>
    <div class="card-body">
        @if(isset($datef))

        <fieldset>
            <legend>Report Criteria:</legend>
            <h3 class="card-title">Generated from {{ $datef }} to {{ $datet }}</h3>

        </fieldset>

        <div class="table-responsive">

        <!-- amount wise -->
        @if(isset($report_type) && $report_type==0)
        <table class="table datatable-button-html5-columns">
            <thead>

                <tr>
                    <th colspan="2">DPD Bucket</th>
                    <th colspan="8">Portfolio Position (PKR)</th>
                    <th colspan="8">No. of Loans (#)</th>
                </tr>
                <tr>
                    <th>Amount Bracket</th>
                    <th>Disbursed Amount</th>
                    <th>[0-30]</th>
                    <th>[31-60]</th>
                    <th>[61-90]</th>
                    <th>[91-120]</th>
                    <th>[121-150]</th>
                    <th>[151-180]</th>
                    <th>[181-360]</th>
                    <th>[More than 360]</th>
                    <th>[0-30]</th>
                    <th>[31-60]</th>
                    <th>[61-90]</th>
.                    <th>[91-120]</th>
                    <th>[121-150]</th>
                    <th>[151-180]</th>
                    <th>[181-360]</th>
                    <th>[More than 360]</th>
                </tr>
                
            </thead>
            <tbody>
                
                
                <tr  class="border-blue">
                    <td>[0-5]</td>
                    
                    @php
                    $grand_disb =    $ReportRow['disb']['disb_5']+$ReportRow['disb']['disb_10']+$ReportRow['disb']['disb_15']+$ReportRow['disb']['disb_20']+$ReportRow['disb']['disb_25']+$ReportRow['disb']['disb_26'];
                    $grand_out_30 =  $ReportRow['30']['slab_5']+$ReportRow['30']['slab_10']+$ReportRow['30']['slab_15']+$ReportRow['30']['slab_20']+$ReportRow['30']['slab_25']+$ReportRow['30']['slab_26'];
                    $grand_out_60 =  $ReportRow['60']['slab_5']+$ReportRow['60']['slab_10']+$ReportRow['60']['slab_15']+$ReportRow['60']['slab_20']+$ReportRow['60']['slab_25']+$ReportRow['60']['slab_26'];
                    $grand_out_90 =  $ReportRow['90']['slab_5']+$ReportRow['90']['slab_10']+$ReportRow['90']['slab_15']+$ReportRow['90']['slab_20']+$ReportRow['90']['slab_25']+$ReportRow['90']['slab_26'];
                    $grand_out_120 = $ReportRow['120']['slab_5']+$ReportRow['120']['slab_10']+$ReportRow['120']['slab_15']+$ReportRow['120']['slab_20']+$ReportRow['120']['slab_25']+$ReportRow['120']['slab_26'];
                    $grand_out_150 = $ReportRow['150']['slab_5']+$ReportRow['150']['slab_10']+$ReportRow['150']['slab_15']+$ReportRow['150']['slab_20']+$ReportRow['150']['slab_25']+$ReportRow['150']['slab_26'];
                    $grand_out_180 = $ReportRow['180']['slab_5']+$ReportRow['180']['slab_10']+$ReportRow['180']['slab_15']+$ReportRow['180']['slab_20']+$ReportRow['180']['slab_25']+$ReportRow['180']['slab_26'];
                    $grand_out_360 = $ReportRow['360']['slab_5']+$ReportRow['360']['slab_10']+$ReportRow['360']['slab_15']+$ReportRow['360']['slab_20']+$ReportRow['360']['slab_25']+$ReportRow['360']['slab_26'];
                    $grand_out_361 = $ReportRow['361']['slab_5']+$ReportRow['361']['slab_10']+$ReportRow['361']['slab_15']+$ReportRow['361']['slab_20']+$ReportRow['361']['slab_25']+$ReportRow['361']['slab_26'];
                    
                    $grand_cl_30 =  $ReportRow['30']['cl_5']+$ReportRow['30']['cl_10']+$ReportRow['30']['cl_15']+$ReportRow['30']['cl_20']+$ReportRow['30']['cl_25']+$ReportRow['30']['cl_26'];
                    $grand_cl_60 =  $ReportRow['60']['cl_5']+$ReportRow['60']['cl_10']+$ReportRow['60']['cl_15']+$ReportRow['60']['cl_20']+$ReportRow['60']['cl_25']+$ReportRow['60']['cl_26'];
                    $grand_cl_90 =  $ReportRow['90']['cl_5']+$ReportRow['90']['cl_10']+$ReportRow['90']['cl_15']+$ReportRow['90']['cl_20']+$ReportRow['90']['cl_25']+$ReportRow['90']['cl_26'];
                    $grand_cl_120 = $ReportRow['120']['cl_5']+$ReportRow['120']['cl_10']+$ReportRow['120']['cl_15']+$ReportRow['120']['cl_20']+$ReportRow['120']['cl_25']+$ReportRow['120']['cl_26'];
                    $grand_cl_150 = $ReportRow['150']['cl_5']+$ReportRow['150']['cl_10']+$ReportRow['150']['cl_15']+$ReportRow['150']['cl_20']+$ReportRow['150']['cl_25']+$ReportRow['150']['cl_26'];
                    $grand_cl_180 = $ReportRow['180']['cl_5']+$ReportRow['180']['cl_10']+$ReportRow['180']['cl_15']+$ReportRow['180']['cl_20']+$ReportRow['180']['cl_25']+$ReportRow['180']['cl_26'];
                    $grand_cl_360 = $ReportRow['360']['cl_5']+$ReportRow['360']['cl_10']+$ReportRow['360']['cl_15']+$ReportRow['360']['cl_20']+$ReportRow['360']['cl_25']+$ReportRow['360']['cl_26'];
                    $grand_cl_361 = $ReportRow['361']['cl_5']+$ReportRow['361']['cl_10']+$ReportRow['361']['cl_15']+$ReportRow['361']['cl_20']+$ReportRow['361']['cl_25']+$ReportRow['361']['cl_26'];
                    @endphp
                    
                    
                    <td align="right">{{ number_format($ReportRow['disb']['disb_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['cl_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['cl_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['cl_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['cl_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['cl_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['cl_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['cl_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['cl_5'],0) }}</td>
                </tr>
                <tr  class="border-blue">
                    <td>[5-10]</td>
                    <td align="right">{{ number_format($ReportRow['disb']['disb_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['slab_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['slab_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['slab_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['slab_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['slab_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['slab_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['cl_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['cl_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['cl_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['cl_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['cl_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['cl_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['cl_10'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['cl_10'],0) }}</td>
                </tr>
                <tr  class="border-blue">
                    <td>[10-15]</td>
                    <td align="right">{{ number_format($ReportRow['disb']['disb_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['slab_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['slab_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['slab_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['slab_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['slab_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['slab_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['cl_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['cl_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['cl_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['cl_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['cl_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['cl_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['cl_15'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['cl_15'],0) }}</td>
                </tr>
                <tr  class="border-blue">
                    <td>[15-20]</td>
                    <td align="right">{{ number_format($ReportRow['disb']['disb_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['cl_20'],0) }}</td>
                </tr>
                <tr  class="border-blue">
                    <td>[20-25]</td>
                    <td align="right">{{ number_format($ReportRow['disb']['disb_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['slab_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['slab_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['slab_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['slab_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['slab_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['slab_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['cl_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['cl_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['cl_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['cl_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['cl_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['cl_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['cl_25'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['cl_25'],0) }}</td>
                </tr>
                <tr  class="border-blue">
                    <td>[25+]</td>
                    <td align="right">{{ number_format($ReportRow['disb']['disb_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['slab_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['slab_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['slab_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['slab_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['slab_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['slab_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['slab_5'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['30']['cl_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['60']['cl_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['90']['cl_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['120']['cl_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['150']['cl_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['180']['cl_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['360']['cl_26'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow['361']['cl_26'],0) }}</td>
                </tr>
                <tr  class="border-blue">
                    <td>Total</td>
                    <td align="right">{{ number_format($grand_disb,0) }}</td>
                    <td align="right">{{ number_format($grand_out_30,0) }}</td>
                    <td align="right">{{ number_format($grand_out_60,0) }}</td>
                    <td align="right">{{ number_format($grand_out_90,0) }}</td>
                    <td align="right">{{ number_format($grand_out_120,0) }}</td>
                    <td align="right">{{ number_format($grand_out_150,0) }}</td>
                    <td align="right">{{ number_format($grand_out_180,0) }}</td>
                    <td align="right">{{ number_format($grand_out_360,0) }}</td>
                    <td align="right">{{ number_format($grand_out_361,0) }}</td>
                    <td align="right">{{ number_format($grand_cl_30,0) }}</td>
                    <td align="right">{{ number_format($grand_cl_60,0) }}</td>
                    <td align="right">{{ number_format($grand_cl_90,0) }}</td>
                    <td align="right">{{ number_format($grand_cl_120,0) }}</td>
                    <td align="right">{{ number_format($grand_cl_150,0) }}</td>
                    <td align="right">{{ number_format($grand_cl_180,0) }}</td>
                    <td align="right">{{ number_format($grand_cl_360,0) }}</td>
                    <td align="right">{{ number_format($grand_cl_361,0) }}</td>
                </tr>
                
            </tbody>
        </table>

        @elseif(isset($report_type) && $report_type==1)
        
        
        
        <!-- ltv wise -->
        <table class="table-bordered col-sm-12">
            <thead>

                <tr>
                    <th colspan="2">DPD Bucket</th>
                    <th colspan="8">LTV Bucket</th>
                    <th colspan="8">No. of Loans (#)</th>
                </tr>
                <tr>
                    <th>LTV Bucket</th>
                    <th>Disbursed Amount</th>
                    <th>[0-30]</th>
                    <th>[31-60]</th>
                    <th>[61-90]</th>
                    <th>[91-120]</th>
                    <th>[121-150]</th>
                    <th>[151-180]</th>
                    <th>[181-360]</th>
                    <th>[More than 360]</th>
                    <th>[0-30]</th>
                    <th>[31-60]</th>
                    <th>[61-90]</th>
                    <th>[91-120]</th>
                    <th>[121-150]</th>
                    <th>[151-180]</th>
                    <th>[181-360]</th>
                    <th>[More than 360]</th>
                </tr>
                
            </thead>
            <tbody>
                
                
                
                <tr  class="border-blue">
                    <td>[< 20%]</td>
                    <td align="right">{{ number_format($ReportRow_LTV['disb']['disb_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['slab_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['cl_20'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['cl_20'],0) }}</td>
                </tr>
                <tr  class="border-blue">
                    <td>[20%-40%]</td>
                    <td align="right">{{ number_format($ReportRow_LTV['disb']['disb_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['slab_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['slab_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['slab_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['slab_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['slab_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['slab_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['slab_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['slab_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['cl_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['cl_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['cl_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['cl_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['cl_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['cl_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['cl_40'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['cl_40'],0) }}</td>

                </tr>
                <tr  class="border-blue">
                    <td>[40%-60%]</td>
                    <td align="right">{{ number_format($ReportRow_LTV['disb']['disb_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['slab_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['slab_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['slab_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['slab_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['slab_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['slab_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['slab_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['slab_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['cl_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['cl_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['cl_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['cl_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['cl_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['cl_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['cl_60'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['cl_60'],0) }}</td>

                </tr>
                <tr  class="border-blue">
                    <td>[60%-80%]</td>
                    <td align="right">{{ number_format($ReportRow_LTV['disb']['disb_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['slab_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['slab_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['slab_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['slab_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['slab_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['slab_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['slab_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['slab_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['cl_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['cl_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['cl_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['cl_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['cl_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['cl_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['cl_80'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['cl_80'],0) }}</td>
                </tr>
                <tr  class="border-blue">
                    <td>[80% plus]</td>
                    <td align="right">{{ number_format($ReportRow_LTV['disb']['disb_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['slab_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['slab_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['slab_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['slab_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['slab_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['slab_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['slab_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['slab_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['30']['cl_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['60']['cl_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['90']['cl_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['120']['cl_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['150']['cl_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['180']['cl_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['360']['cl_81'],0) }}</td>
                    <td align="right">{{ number_format($ReportRow_LTV['361']['cl_81'],0) }}</td>
                </tr>
                    @php
                    $ltv_grand_disb =    $ReportRow_LTV['disb']['disb_20']+$ReportRow_LTV['disb']['disb_40']+$ReportRow_LTV['disb']['disb_60']+$ReportRow_LTV['disb']['disb_80']+$ReportRow_LTV['disb']['disb_81'];
                    $ltv_grand_30 =    $ReportRow_LTV_LTV['30']['slab_20']+$ReportRow_LTV['30']['slab_40']+$ReportRow_LTV['30']['slab_60']+$ReportRow_LTV['30']['slab_80']+$ReportRow_LTV['30']['slab_81'];
                    $ltv_grand_60 =    $ReportRow_LTV_LTV['60']['slab_20']+$ReportRow_LTV['60']['slab_40']+$ReportRow_LTV['60']['slab_60']+$ReportRow_LTV['60']['slab_80']+$ReportRow_LTV['60']['slab_81'];
                    $ltv_grand_90 =    $ReportRow_LTV_LTV['90']['slab_20']+$ReportRow_LTV['90']['slab_40']+$ReportRow_LTV['90']['slab_60']+$ReportRow_LTV['90']['slab_80']+$ReportRow_LTV['90']['slab_81'];
                    $ltv_grand_120 =    $ReportRow_LTV_LTV['120']['slab_20']+$ReportRow_LTV['120']['slab_40']+$ReportRow_LTV['120']['slab_60']+$ReportRow_LTV['120']['slab_80']+$ReportRow_LTV['120']['slab_81'];
                    $ltv_grand_150 =    $ReportRow_LTV_LTV['150']['slab_20']+$ReportRow_LTV['150']['slab_40']+$ReportRow_LTV['150']['slab_60']+$ReportRow_LTV['150']['slab_80']+$ReportRow_LTV['150']['slab_81'];
                    $ltv_grand_180 =    $ReportRow_LTV_LTV['180']['slab_20']+$ReportRow_LTV['180']['slab_40']+$ReportRow_LTV['180']['slab_60']+$ReportRow_LTV['180']['slab_80']+$ReportRow_LTV['180']['slab_81'];
                    $ltv_grand_360 =    $ReportRow_LTV_LTV['360']['slab_20']+$ReportRow_LTV['360']['slab_40']+$ReportRow_LTV['360']['slab_60']+$ReportRow_LTV['360']['slab_80']+$ReportRow_LTV['360']['slab_81'];
                    $ltv_grand_361 =    $ReportRow_LTV_LTV['361']['slab_20']+$ReportRow_LTV['361']['slab_40']+$ReportRow_LTV['361']['slab_60']+$ReportRow_LTV['361']['slab_80']+$ReportRow_LTV['361']['slab_81'];
                    $ltv_grand_cl_30 =    $ReportRow_LTV_LTV['30']['cl_20']+$ReportRow_LTV['30']['cl_40']+$ReportRow_LTV['30']['cl_60']+$ReportRow_LTV['30']['cl_80']+$ReportRow_LTV['30']['cl_81'];
                    $ltv_grand_cl_60 =    $ReportRow_LTV_LTV['60']['cl_20']+$ReportRow_LTV['60']['cl_40']+$ReportRow_LTV['60']['cl_60']+$ReportRow_LTV['60']['cl_80']+$ReportRow_LTV['60']['cl_81'];
                    $ltv_grand_cl_90 =    $ReportRow_LTV_LTV['90']['slab_20']+$ReportRow_LTV['90']['cl_40']+$ReportRow_LTV['90']['cl_60']+$ReportRow_LTV['90']['cl_80']+$ReportRow_LTV['90']['cl_81'];
                    $ltv_grand_cl_120 =    $ReportRow_LTV_LTV['120']['slab_20']+$ReportRow_LTV['120']['cl_40']+$ReportRow_LTV['120']['cl_60']+$ReportRow_LTV['120']['cl_80']+$ReportRow_LTV['120']['cl_81'];
                    $ltv_grand_cl_150 =    $ReportRow_LTV_LTV['150']['slab_20']+$ReportRow_LTV['150']['cl_40']+$ReportRow_LTV['150']['cl_60']+$ReportRow_LTV['150']['cl_80']+$ReportRow_LTV['150']['cl_81'];
                    $ltv_grand_cl_180 =    $ReportRow_LTV_LTV['180']['slab_20']+$ReportRow_LTV['180']['cl_40']+$ReportRow_LTV['180']['cl_60']+$ReportRow_LTV['180']['cl_80']+$ReportRow_LTV['180']['cl_81'];
                    $ltv_grand_cl_360 =    $ReportRow_LTV_LTV['360']['slab_20']+$ReportRow_LTV['360']['cl_40']+$ReportRow_LTV['360']['cl_60']+$ReportRow_LTV['360']['cl_80']+$ReportRow_LTV['360']['cl_81'];
                    $ltv_grand_cl_361 =    $ReportRow_LTV_LTV['361']['slab_20']+$ReportRow_LTV['361']['cl_40']+$ReportRow_LTV['361']['cl_60']+$ReportRow_LTV['361']['cl_80']+$ReportRow_LTV['361']['cl_81'];
                    @endphp
                
                
                <tr  class="border-blue">
                    <td>Total</td>
                    <td align="right">{{ number_format($ltv_grand_disb,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_30,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_60,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_90,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_120,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_150,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_180,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_360,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_361,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_cl_30,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_cl_60,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_cl_90,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_cl_120,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_cl_150,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_cl_180,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_cl_360,0) }}</td>
                    <td align="right">{{ number_format($ltv_grand_cl_361,0) }}</td>
                </tr>
                
            </tbody>
        </table>

        
        @else
        <!-- city wise -->
        <table class="table-bordered col-sm-12">
            <thead>

                <tr>
                    <th colspan="2">DPD Bucket</th>
                    <th colspan="8">LTV Bucket</th>
                    <th colspan="8">No. of Loans (#)</th>
                </tr>
                <tr>
                    <th>City Name</th>
                    <th>Disbursed Amount</th>
                    <th>[0-30]</th>
                    <th>[31-60]</th>
                    <th>[61-90]</th>
                    <th>[91-120]</th>
                    <th>[121-150]</th>
                    <th>[151-180]</th>
                    <th>[181-360]</th>
                    <th>[More than 360]</th>
                    <th>[0-30]</th>
                    <th>[31-60]</th>
                    <th>[61-90]</th>
                    <th>[91-120]</th>
                    <th>[121-150]</th>
                    <th>[151-180]</th>
                    <th>[181-360]</th>
                    <th>[More than 360]</th>
                </tr>
                
            </thead>
            <tbody>
                @php
                    $grand_disb = $grand_outs = $grand_cl = 0;
                @endphp
                

                @foreach($slab_city as $branch=>$value)
                <tr class="border-blue">
                    <td>{{ $branch }}</td>
                    <td align="right">{{ number_format($value['disb'],0) }}</td>
                    <td align="right">{{ $value['30'] && $value['30']["outs"] ? number_format($value['30']["outs"],0) : 0 }}</td>
                    <td align="right">{{ $value['60'] && $value['60']["outs"] ? number_format($value['60']["outs"],0) : 0 }}</td>
                    <td align="right">{{ $value['90'] && $value['90']["outs"] ? number_format($value['90']["outs"],0) : 0 }}</td>
                    <td align="right">{{ $value['120'] && $value['120']["outs"] ? number_format($value['120']["outs"],0) : 0 }}</td>
                    <td align="right">{{ $value['150'] && $value['150']["outs"] ? number_format($value['150']["outs"],0) : 0 }}</td>
                    <td align="right">{{ $value['180'] && $value['180']["outs"] ? number_format($value['180']["outs"],0) : 0 }}</td>
                    <td align="right">{{ $value['360'] && $value['360']["outs"] ? number_format($value['360']["outs"],0) : 0 }}</td>
                    <td align="right">{{ $value['361'] && $value['361']["outs"] ? number_format($value['361']["outs"],0) : 0 }}</td>
                    <td align="right">{{ $value['30'] && $value['30']["cl"] ? number_format($value['30']["cl"],0) : 0 }}</td>
                    <td align="right">{{ $value['60'] && $value['60']["cl"] ? number_format($value['60']["cl"],0) : 0 }}</td>
                    <td align="right">{{ $value['90'] && $value['90']["cl"] ? number_format($value['90']["cl"],0) : 0 }}</td>
                    <td align="right">{{ $value['120'] && $value['120']["cl"] ? number_format($value['120']["cl"],0) : 0 }}</td>
                    <td align="right">{{ $value['150'] && $value['150']["cl"] ? number_format($value['150']["cl"],0) : 0 }}</td>
                    <td align="right">{{ $value['180'] && $value['180']["cl"] ? number_format($value['180']["cl"],0) : 0 }}</td>
                    <td align="right">{{ $value['360'] && $value['360']["cl"] ? number_format($value['360']["cl"],0) : 0 }}</td>
                    <td align="right">{{ $value['361'] && $value['361']["cl"] ? number_format($value['361']["cl"],0) : 0 }}</td>
                </tr>
                    @php
                    $city_grand_disb +=    $value['disb'];
                    $city_grand_30 =    $value['30']['outs'];
                    $city_grand_60 =    $value['60']['outs'];
                    $city_grand_90 =    $value['90']['outs'];
                    $city_grand_120 =    $value['120']['outs'];
                    $city_grand_150 =    $value['150']['outs'];
                    $city_grand_180 =    $value['180']['outs'];
                    $city_grand_360 =    $value['360']['outs'];
                    $city_grand_361 =    $value['361']['outs'];
                    $city_grand_cl_30 =    $value['30']['cl'];
                    $city_grand_cl_60 =    $value['60']['cl'];
                    $city_grand_cl_90 =    $value['90']['cl'];
                    $city_grand_cl_120 =    $value['120']['cl'];
                    $city_grand_cl_150 =    $value['150']['cl'];
                    $city_grand_cl_180 =    $value['180']['cl'];
                    $city_grand_cl_360 =    $value['360']['cl'];
                    $city_grand_cl_361 =    $value['361']['cl'];
                    @endphp
                    
                @endforeach
                
                <tr  class="border-blue">
                    <td>Total</td>
                    <td align="right">{{ number_format($city_grand_disb,0) }}</td>
                    <td align="right">{{ number_format($city_grand_30,0) }}</td>
                    <td align="right">{{ number_format($city_grand_60,0) }}</td>
                    <td align="right">{{ number_format($city_grand_90,0) }}</td>
                    <td align="right">{{ number_format($city_grand_120,0) }}</td>
                    <td align="right">{{ number_format($city_grand_150,0) }}</td>
                    <td align="right">{{ number_format($city_grand_180,0) }}</td>
                    <td align="right">{{ number_format($city_grand_360,0) }}</td>
                    <td align="right">{{ number_format($city_grand_361,0) }}</td>
                    <td align="right">{{ number_format($city_grand_cl_30,0) }}</td>
                    <td align="right">{{ number_format($city_grand_cl_60,0) }}</td>
                    <td align="right">{{ number_format($city_grand_cl_90,0) }}</td>
                    <td align="right">{{ number_format($city_grand_cl_120,0) }}</td>
                    <td align="right">{{ number_format($city_grand_cl_150,0) }}</td>
                    <td align="right">{{ number_format($city_grand_cl_360,0) }}</td>
                    <td align="right">{{ number_format($city_grand_cl_361,0) }}</td>
                </tr>
                
            </tbody>
        </table>
        </div>
        @endif

        @else

        <form method="POST" action="{{ route('reports.agingamountreport') }}"  role="form" enctype="multipart/form-data">
            @csrf
            <div class="box box-info padding-1">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            {{ Form::label('Report Type:') }}
                            {{ Form::select('report_type',['Amount-Wise','Percentage-Wise','City-Wise'], null, ['class' => 'form-control' . ($errors->has('report_type') ? ' is-invalid' : '')]) }}
                            {!! $errors->first('report_type', '<p class="invalid-feedback">:message</p>') !!}
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
