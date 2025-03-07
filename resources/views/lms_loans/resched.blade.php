@extends('layouts.master')
@section('page_title', 'Manage Financing')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">

        <h6 class="card-title">Rescheduling</h6>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#add-tt" class="nav-link active" data-toggle="tab">Rescheduling</a></li>
        </ul>


        <div class="menuopt tab-content">
            <div class="form-group">
                <label for="amount">Method:</label>
                <select name="method" class="form-control select" >
                    <option value="1">Increase</option>
                    <option value="0">Decrease</option>
                </select>
            </div>
            <div class="form-group">
                <label for="matdate">Maturity Date:</label>
                <input type="date" name="matdate" id="matdate" class="form-control" />
            </div>
            <div class="form-group">
                <input type="button" value="Proceed" class="btn btn-primary col-md-12" />
            </div>
        </div>
    </div>
</div>

@endsection
