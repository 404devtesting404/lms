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


            @if(isset($datef))

            <fieldset>
                <legend>Report Criteria:</legend>
                <h3 class="card-title">Generated from {{ $datef }} to {{ $datet }}</h3>

            </fieldset>

            <form method="POST" action="{{ route('loans.postrenewkibor') }}"  role="form" enctype="multipart/form-data">
                @csrf
                <table class="table">
                    <thead>
                        <tr class="datatable-header center"  >
                            <th></th>
                            <th>Sr.#</th>
                            <th>Borrower Name</th>
                            <th>Installment#</th>
                            <th>Renewal Date</th>
                            <th>Renewal Rate</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($loanData as $loanData)
                        <tr  class="border-blue">
                            <td><input type="checkbox" name="set[]" value="{{ $loanData }}" /></td>
                            <td>{{ ++$i }}</td>
                            <td>{{ $loanData->fname." ".$loanData->mname." ".$loanData->lname }}</td>
                            <td>{{ $loanData->installment_no }}</td>
                            <td>{{ date("j M Y", strtotime($loanData->kibor_date)) }}</td>
                            <td>{{ $loanData->new_kibor_rate ? $loanData->new_kibor_rate."%" : "Not Set" }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                <button class="btn btn-success col-md-12" type="submit">Renew Kibor</button> 
            </form>

            @else

            <form method="POST" action="{{ route('loans.setrenewkibor') }}"  role="form" enctype="multipart/form-data">
                @csrf
                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::label('Date From:') }}
                                {{ Form::date('datefrom', null, ['class' => 'form-control' . ($errors->has('datefrom') ? ' is-invalid' : ''), 'placeholder' => 'Date From:']) }}
                                {!! $errors->first('due_id', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            <div class="col-md-6">
                                {{ Form::label('Date To:') }}
                                {{ Form::date('dateto', null, ['class' => 'form-control' . ($errors->has('dateto') ? ' is-invalid' : ''), 'placeholder' => 'Date To:']) }}
                                {!! $errors->first('loan_id', '<p class="invalid-feedback">:message</p>') !!}
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="form-control btn btn-primary col-md-12">Generate</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>


            @endif
        </div>
    </div>
</div>

@endsection
