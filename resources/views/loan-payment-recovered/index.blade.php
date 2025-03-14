@extends('layouts.master')
@section('page_title', 'Financing Payment Recovered')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Financing Payment Recovered') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('loan-payment-recovereds.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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

<!--										<th>Due Id</th>-->
										<th>Financing Id</th>
										<th>Total</th>
										<th>Principle</th>
										<th>Profit</th>
										<th>Settlement</th>
										<th>FED</th>
										<th>Penalty</th>
										<th>Recovered By</th>
										<th>Recovered Date</th>
										<th>Bank Slip Id</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($loanPaymentRecovereds as $loanPaymentRecovered)
                                        <tr>
                                            <td>{{ ++$i }}</td>

<!--											<td>{{ $loanPaymentRecovered->due_id }}</td>-->
											<td>{{ $loanPaymentRecovered->loan_id }}</td>
											<td>{{ number_format($loanPaymentRecovered->amount_total,0) }}</td>
											<td>{{ number_format($loanPaymentRecovered->amount_pr,0) }}</td>
											<td>{{ number_format($loanPaymentRecovered->amount_mu,0) }}</td>
											<td>{{ number_format($loanPaymentRecovered->amount_settlement,0) }}</td>
											<td>{{ number_format($loanPaymentRecovered->amount_fed,0) }}</td>
											<td>{{ number_format($loanPaymentRecovered->amount_penalty,0) }}</td>
											<td>{{ $loanPaymentRecovered->recovered_by }}</td>
											<td>{{ $loanPaymentRecovered->recovered_date }}</td>
											<td>{{ $loanPaymentRecovered->bank_slip_id }}</td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('loan-payment-recovereds.show',$loanPaymentRecovered->id) }}"><i class="icon-eye"></i>Show</a>
                                                        <a class="dropdown-item" href="{{ route('loan-payment-recovereds.edit',$loanPaymentRecovered->id)  }}"><i class="icon-pencil"></i> Edit</a>
                                                        <a class="dropdown-item" href="{{ route('loan-payment-recovereds.destroy',$loanPaymentRecovered->id) }}"><i class="icon-trash"></i> Delete</a>
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
