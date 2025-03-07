@extends('layouts.master')
@section('page_title', 'Page Activity')
@section('content')
<style>
    th,td{ padding: 5px !important;}
</style>
    {{--Events Calendar Begins--}}
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Page Activity</h5>

        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label>Users</label>
                    <select name="user" required="required" id="user" class="form-control select-search">
                        <option value="">Select</option>
                        @foreach($users as $val)
                            <option value="{{$val->id}}">{{$val->name}}</option>
                        @endforeach
                    </select>

                </div>
                <div class="col-md-4">
                    <label>Date</label>
                    <input id="date" type="date" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}">
                </div>
                <div class="col-md-2">
                    <br>
                    <label style="padding-top: 15px">Show all &nbsp; <input style="height: 20px;width: 20px" type="checkbox" id="show_all_activity"></label>
                </div>
                <div class="col-md-2">
                    <br>
                    <button id="search_log" type="button" onclick="getUserActivity()" class="btn btn-primary btn-xs"><i class="icon-search4"></i> Search</button>
                </div>
            </div>
            <br>
            <div class="col-12">
                <div id="logs_area">
                </div>
            </div>
        </div>
    </div>
    {{--Events Calendar Ends--}}
@endsection
