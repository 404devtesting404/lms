@extends('layouts.master')
@section('page_title', 'Renew Kibor Rate')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Renew Kibor Rate') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('loan-kibor-rates.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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

										<th>Kibor Rate</th>
										<th>Start Date</th>
										<th>End Date</th>
										<th>Status</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($loanKiborRates as $loanKiborRate)
                                        <tr>
                                            <td>{{ ++$i }}</td>

											<td>{{ $loanKiborRate->kibor_rate }}</td>
											<td>{{ date("j M Y", strtotime($loanKiborRate->start_date)) }}</td>
											<td>{{ date("j M Y", strtotime($loanKiborRate->end_date)) }}</td>
											<td>{{ $loanKiborRate->status ? "Active" : "Inactive" }}</td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{route('loan-kibor-rates.show',$loanKiborRate->id) }}">Show</a>
                                                        <a class="dropdown-item" href="{{ route('loan-kibor-rates.edit',$loanKiborRate->id) }}">Edit</a>
                                                        <a class="dropdown-item" href="{{ route('loan-kibor-rates.destroy',$loanKiborRate->id) }}">Delete</a>
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
