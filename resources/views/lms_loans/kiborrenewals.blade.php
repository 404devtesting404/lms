@extends('layouts.master')
@section('page_title', 'Kibor Renewal Schedule')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Kibor Renewal Schedule</h6>
        {!! Qs::getPanelOptions() !!}


    </div>

    <div class="card-body">
        <div class="tab-content">

            <div class="tab-pane fade show active" >
                <table class="table datatable-button-html5-columns">
                    <tr <?php echo 'style="background-color:#11823B !important; color:#FFFFFF !important; text-align: center; font-weight: 800;"' ?>>
                        <td align='center'>S.No.</td>
                        <td align='center'>Installment No.</td>
                        <td align='center'>Kibor Renewal Date</td>
                        <td align='center'>Kibor Rate</td>
                    </tr>
                    @php
                    $i = 1
                    @endphp
                    @foreach($KiborRevData as $KiborRevData)
                    <tr <?php echo $i%2==0 ? 'style="background-color:#BEE5EB !important; text-align: center; font-weight: 800;"' : 'style="background-color:#dac484 !important; text-align: center; font-weight: 800;" ' ?>>
                        <td>{{ $i++ }}</td>
                        <td>{{ $KiborRevData->installment_no }}</td>
                        <td>{{ date("j M Y", strtotime($KiborRevData->start_date)) }}</td>
                        <td>{{ $KiborRevData->kibor_rate!=0 ? $KiborRevData->kibor_rate."%" : "-" }}</td>
                        
                    </tr>
                    @endforeach
                    
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
