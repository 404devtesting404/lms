@extends('layouts.master')
@section('page_title', 'Loans Fee')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Loans Fee') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('loans-fees.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Loanid</th>
										<th>Processingfees</th>
										<th>Processingfeesstatus</th>
										<th>Fedfees</th>
										<th>Fedfeesstatus</th>
										<th>Legalfeescompanyid</th>
										<th>Legalfees</th>
										<th>Legalfeesstatus</th>
										<th>Valuationcompanyid</th>
										<th>Valuationfees</th>
										<th>Valuationfeesstatus</th>
										<th>Incomeestcompanyid</th>
										<th>Incomeestfees</th>
										<th>Incomeestfeesstatus</th>
										<th>Stamppaperfees</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($loansFees as $loansFee)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $loansFee->loanId }}</td>
											<td>{{ $loansFee->processingFees }}</td>
											<td>{{ $loansFee->processingFeesStatus }}</td>
											<td>{{ $loansFee->fedFees }}</td>
											<td>{{ $loansFee->fedFeesStatus }}</td>
											<td>{{ $loansFee->legalFeesCompanyId }}</td>
											<td>{{ $loansFee->legalFees }}</td>
											<td>{{ $loansFee->legalFeesStatus }}</td>
											<td>{{ $loansFee->valuationCompanyId }}</td>
											<td>{{ $loansFee->valuationFees }}</td>
											<td>{{ $loansFee->valuationFeesStatus }}</td>
											<td>{{ $loansFee->incomeEstCompanyId }}</td>
											<td>{{ $loansFee->incomeEstFees }}</td>
											<td>{{ $loansFee->incomeEstFeesStatus }}</td>
											<td>{{ $loansFee->stampPaperFees }}</td>

                                            <td>
                                                <form action="{{ route('loans-fees.destroy',$loansFee->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('loans-fees.show',$loansFee->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('loans-fees.edit',$loansFee->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                

@endsection
