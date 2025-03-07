@extends('layouts.master')
@section('page_title', 'Loan Takaful')
@section('content')

    <div class="card">
        <div class="card-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Loan Takaful') }}
                            </span>

                <div class="float-right">
                    <a href="{{ route('loan-takaful.create') }}" class="btn btn-primary btn-sm float-right"
                       data-placement="left">
                        {{ __('Create New') }}
                    </a>
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="card-body">
            <div class="box box-info padding-1">
                <div class="box-body">
                    <form action="{{ route('loan-takaful.takaful.report') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label>Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ isset($startDate) ? $startDate : '' }}">
                            </div>
                            <div class="col-md-3">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ isset($endDate) ? $endDate : '' }}">
                            </div>
                            <div class="col-md-3">
                                <label>Type</label>
                                <select class="form-control" name="type">
                                    <option value="3" {{ isset($type) && $type == 3 ? 'selected' : '' }}>Select Takaful</option>
                                    <option value="1" {{ isset($type) && $type == 1 ? 'selected' : '' }}>Life Takaful</option>
                                    <option value="0" {{ isset($type) && $type == 0 ? 'selected' : '' }}>Property Takaful</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary mt-4">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if(isset($loanTakafuls))
                <div class="table-responsive">
                    <table class="table datatable-button-html5-columns">
                        <thead class="thead">
                        <tr>
                            <th>No</th>
                            <th>Customer Name</th>
                            <th>Customer CNIC</th>
                            <th>Account Number</th>
                            <th>Disbursed Amount</th>
                            <th>Disbursed Date</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Policy Number</th>
                            <th>Renewal Date</th>

                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($loanTakafuls as $key => $loanTakaful)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $loanTakaful->loan_history->loan_borrower->fname .' '. $loanTakaful->loan_history->loan_borrower->mname.' '. $loanTakaful->loan_history->loan_borrower->lname }}</td>
                                <td>{{ $loanTakaful->loan_history->loan_borrower->cnic }}</td>
                                <td>{{ $loanTakaful->loan_id }}</td>
                                <td>{{ number_format($loanTakaful->covered_amount) }}</td>
                                <td>{{ date('d-m-Y', strtotime($loanTakaful->loan_history->disb_date)) }}</td>
                                <td>{{ $loanTakaful->type==0 ? "Property" : "Life" }}</td>
                                <td>{{ $loanTakaful->start_date }}</td>
                                <td>{{ $loanTakaful->end_date }}</td>
                                <td>{{ $loanTakaful->policy_number }}</td>
                                <td>{{ $loanTakaful->renewal_date }}</td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn btn-outline-primary dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item"
                                               href="{{ route('loan-takaful.show',$loanTakaful->id) }}"><i
                                                        class="icon-eye"></i>Show</a>
                                            <a class="dropdown-item"
                                               href="{{ route('loan-takaful.edit',$loanTakaful->id) }}"><i
                                                        class="icon-pencil"></i>Edit</a>
                                            <a class="dropdown-item"
                                               href="{{ route('loan-takaful.destroy',$loanTakaful->id) }}"><i
                                                        class="icon-trash"></i>Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>

@endsection
