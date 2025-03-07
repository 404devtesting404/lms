@extends('layouts.master')
@section('page_title', 'Loan Modification')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Loan Modification') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('loan-modifications.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
										<th>Modification</th>
										<th>Amount</th>
										<th>Modify By</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($loanModifications as $loanModification)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $loanModification->loan_id }}</td>
											<td>{{ $loanModification->modification }}</td>
											<td>{{ $loanModification->amount }}</td>
											<td>{{ $loanModification->modify_by }}</td>

                                            <td>
                                                <form action="{{ route('loan-modifications.destroy',$loanModification->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('loan-modifications.show',$loanModification->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('loan-modifications.edit',$loanModification->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
