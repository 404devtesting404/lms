@extends('layouts.master')
@section('page_title', 'Manage Financing')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">

        <h6 class="card-title">Enhancement</h6>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#add-tt" class="nav-link active" data-toggle="tab">Enhancement</a></li>
        </ul>


        <div class="menuopt tab-content">
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="text" name="amount" id="amount" class="form-control" />
                </div>
                <div class="form-group">
                    <label for="matdate">Enhancement Date:</label>
                    <input type="date" name="enhdate" id="enhdate" class="form-control" />
                </div>
                <div class="form-group">
                    <input type="button" value="Proceed" class="btn btn-primary col-md-12" />
                </div>
        </div>
    </div>
</div>

@endsection
