@extends('layouts.master')
@section('page_title', 'General Reports')
@section('content')

<div class="card card-default">
    <div class="card-header">
        <span class="card-title">General Reports</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('reports.generalreport') }}"  role="form" enctype="multipart/form-data">
            @csrf
            <div class="box box-info padding-1">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            {{ Form::label('Report Type:') }}
                            {{ Form::select('report_type',
                                [
                                    'booking'=> 'Booking Report',
                                    'disb'=> 'Disbursement Report',
                                    'due'=> 'Due Report',
                                    'od'=> 'OverDue Report',
                                    'rep'=> 'Repayment Report',
                                    'rent'=> 'Outstanding Portfolio Report',
                                    'takprop'=> 'Property Takaful Renewal Report',
                                    'taklife'=> 'Life Takaful Renewal Report',
                                    'scrs'=> 'SCRS Report',
                                    'cancled'=> 'Cancelled Report',
                                    'earlySettlement'=> 'Early Settlement Report'
                                ],
                                        null, ['class' => 'report_type form-control' . ($errors->has('report_type') ? ' is-invalid' : '')]) }}
                            {!! $errors->first('report_type', '<p class="invalid-feedback">:message</p>') !!}
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            {{ Form::label('Date From:') }}
                            {{ Form::date('datefrom', null, ['class' => 'datefrom form-control' . ($errors->has('datefrom') ? ' is-invalid' : ''), 'placeholder' => 'Date From:']) }}
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

    </div>
</div>
<script>
    $(document).ready(function(){
        $(".report_type").on('change', function(){
            //alert($(this).val())
            if($(this).val()=='rent' || $(this).val()=='scrs'){
                $(".datefrom").val("2022-10-01");
                $(".datefrom").prop("disabled","disabled");
                //alert("done");
            }
            if($(this).val()=='od'){
                $(".datefrom").val("2022-10-01");
                // $(".datefrom").prop("disabled","disabled");
                //alert("done");
            }
        });

    });
</script>
@endsection
