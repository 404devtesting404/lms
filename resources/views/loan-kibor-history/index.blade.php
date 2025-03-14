@extends('layouts.master')
@section('page_title', 'Loan Kibor History')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Loan Kibor History') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('loan-kibor-histories.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                        <div class="table-responsive">
                            <table class="table datatable-button-html5-columns">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>

										<th>Loan Id</th>
										<th>Kibor Rate Id</th>
										<th>Start Date</th>
										<th>End Date</th>
										<th>Status</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($loanKiborHistories as $loanKiborHistory)
                                        <tr>
                                            <td>{{ ++$i }}</td>

											<td>{{ $loanKiborHistory->loan_id }}</td>
											<td>{{ $loanKiborHistory->kibor_rate_id }}</td>
											<td>{{ $loanKiborHistory->start_date }}</td>
											<td>{{ $loanKiborHistory->end_date }}</td>
											<td>{{ $loanKiborHistory->status }}</td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('loan-kibor-histories.show',$loanKiborHistory->id) }}">Show</a>
                                                        <a class="dropdown-item" href="{{ route('loan-kibor-histories.edit',$loanKiborHistory->id) }}">Edit</a>
                                                        <a class="dropdown-item" href="{{ route('loan-kibor-histories.destroy',$loanKiborHistory->id) }}">Delete</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


@endsection
