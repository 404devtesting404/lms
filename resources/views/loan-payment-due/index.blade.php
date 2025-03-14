@extends('layouts.master')
@section('page_title', 'Financing Payment Due')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Financing Payment Due') }}
                            </span>

                             <div class="float-right">
<!--                                <a href="{{ route('loan-payment-dues.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>-->
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

										<th>A/c#</th>
										<th>Inst.#</th>
										<th>Due Date</th>
										<th>Amount</th>
										<th>Principle</th>
										<th>Profit</th>
										<th>Status</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($loanPaymentDues as $loanPaymentDue)
                                        <tr>
                                            <td>{{ ++$i }}</td>

											<td>{{ $loanPaymentDue->loan_id }}</td>
											<td>{{ $loanPaymentDue->installment_no }}</td>
											<td>{{ $loanPaymentDue->due_date }}</td>
											<td>{{ $loanPaymentDue->amount_total }}</td>
											<td>{{ $loanPaymentDue->amount_pr }}</td>
											<td>{{ $loanPaymentDue->amount_mu }}</td>
                                             <td>
                                                 <?php echo $loanPaymentDue->payment_status ? '<span class="badge badge-pill badge-success">&nbsp;&nbsp;&nbsp;&nbsp;PAID&nbsp;&nbsp;&nbsp;&nbsp;</span>' : '<span class="badge badge-pill badge-danger">UN-PAID</span>' ?>
                                             </td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('loan-payment-dues.show',$loanPaymentDue->id) }}"><i class="icon-eye"></i>Show</a>
                                                        <a class="dropdown-item" href="{{ route('loan-payment-dues.edit',$loanPaymentDue->id)  }}"><i class="icon-pencil"></i> Edit</a>
                                                        <a class="dropdown-item" href="{{ route('loan-payment-dues.destroy',$loanPaymentDue->id) }}"><i class="icon-trash"></i> Delete</a>
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
