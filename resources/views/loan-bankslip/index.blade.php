@extends('layouts.master')
@section('page_title', 'Financing Bankslip')
@section('content')


                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Financing Bankslip') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('loan-bankslips.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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

										<th>Name</th>
										<th>Amount</th>
										<th>Bank Account</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($loanBankslips as $loanBankslip)
                                        <tr>
                                            <td>{{ ++$i }}</td>

											<td>{{ $loanBankslip->name }}</td>
											<td>{{ number_format($loanBankslip->amount_sum) }}</td>
											<td>{{ $loanBankslip->bank_name }}</td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('loan-bankslips.show',$loanBankslip->id) }}"><i class="icon-eye"></i>Show</a>
                                                        <a class="dropdown-item" href="{{ route('loan-bankslips.edit',$loanBankslip->id)  }}"><i class="icon-edit"></i> Edit</a>
                                                        <a class="dropdown-item" href="{{ route('loan-bankslips.destroy',$loanBankslip->id) }}"><i class="icon-trash"></i> Delete</a>
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
