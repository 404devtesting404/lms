@extends('layouts.master')
@section('page_title', 'Manage Financing')
@section('content')
    <style>

        .dot_green {
            height: 25px;
            width: 25px;
            background-color: green;
            border-radius: 50%;
            display: inline-block;
        }

        .dot_red {
            height: 25px;
            width: 25px;
            background-color: red;
            border-radius: 50%;
            display: inline-block;
        }

    </style>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage LOS data</h6>

        </div>

        <div class="card-body">
            <table class="table datatable-button-html5-columns">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Cell</th>
                    <th>Email Verified</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users['data'] as $userVal)
                    <tr>
                        <td>{{$loop->iteration }}</td>
                        <td>{{$userVal['user']['name'] }}</td>
                        <td>{{$userVal['user']['email'] }}</td>
                        <td>{{$userVal['user']['mobile_no'] }}</td>
                        <td>
                            @if(empty($userVal['user']['email_verified_at']))
                                <span class="dot_red"></span>
                            @else
                                <span class="dot_green"></span>
                            @endif
                        </td>
                        <td>{{Qs::dateFormat($userVal['user']['created_at']) }}</td>
                        <td>
                            @if(!\App\Helpers\Qs::CheckUserLoanPresent($userVal['user']['cnic']))
                            <div class="dropdown">
                                <button class="btn btn btn-outline-primary dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item"
{{--                                       href="{{ route('loans.losdataby_user').'?id='.$userVal['id']  }}">--}}
                                       href="{{ route('loans.addloan', $userVal['id']) }}">
                                        <i class="icon-eye"></i>Add Loan</a>

                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>

                @endforeach

                </tbody>
            </table>
        </div>


    </div>

@endsection
