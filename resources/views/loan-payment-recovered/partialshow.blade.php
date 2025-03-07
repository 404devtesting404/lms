@extends('layouts.master')
@section('page_title', 'Partial Payment')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Partial Payment</h6>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#add-tt" class="nav-link active" data-toggle="tab">Partial Payment</a></li>
        </ul>


        <div class="tab-content">

            <div class="tab-pane fade show active" id="add-tt">


                <div class="col-md-8">
                        <form method="post" action="{{ route('loan-payment-recovereds.store_partial') }}">
                            @csrf
                            <input type="hidden" name="loanId" value="{{$loanId}}" />
                            <div class="form-group row">
                                <br>
                                <div class="row col-md-12">
                                    <label class="col-md-3 col-form-label font-weight-semibold">Partial Percent:</label>
                                    <input name="percent" type="text" value="{{ isset($percent) ? $percent : "" }}" min="1" max="100" class="col-md-9 form-control  col-form-label font-weight-semibold" placeholder="Partial Percent">
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <label class="col-md-3 col-form-label font-weight-semibold">Date:</label>
                                    <input name="date" type="date" value="{{ isset($showpartial) ? $showpartial : "" }}" class="col-md-9 form-control  col-form-label font-weight-semibold" placeholder="Date">
                                </div>
                            </div>
                            @if(isset($percent))
                            <br><br>
                            <h4>Partial Calculations:</h4>
                            <span>
                                <table width="60%" border="1" cellpadding="5" bordercolor="#CCCCCC">
                                    <tr><td>Partial Percent:</td><td align="right"> <strong>{{ $percent }}%</strong></td></tr>
                                    <tr><td>Total Outstanding:</td><td align="right"> <strong>{{ number_format($outstanding,0)  }}</strong></td></tr>
                                    <tr><td>Partial Amount:</td><td align="right"> <strong>{{ number_format($partial,0) }} </strong></td></tr>
                                    <tr><td>Upper Partial:</td><td align="right"> <strong>{{ number_format($upper_partial,0) }} </strong></td></tr>
                                    <tr><td>Upper Percent:</td><td align="right"> <strong>{{ $markup_percent }}% </strong></td></tr>
                                    <tr><td>Extra Partial:</td><td align="right"> <strong>{{ number_format($markup,0) }} </strong></td></tr>
                                </table>
                            </span>
                            <br><br>
                            @endif

                            <div class="form-group row">
                                <button type="submit" class="col-md-12 btn btn-success float-right" >Save and Pay</button>
                            </div>
                        </form>
                </div>


            </div>


        </div>
    </div>
</div>

{{--TimeTable Ends--}}

@endsection
