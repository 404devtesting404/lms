@extends('layouts.master')
@section('page_title', 'Company Income Est')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Company Income Est') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('company-income-ests.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
										<th>Status</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($companyIncomeEsts as $companyIncomeEst)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $companyIncomeEst->name }}</td>
											<td>{{ $companyIncomeEst->status }}</td>

                                            <td>
                                                <form action="{{ route('company-income-ests.destroy',$companyIncomeEst->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('company-income-ests.show',$companyIncomeEst->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('company-income-ests.edit',$companyIncomeEst->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
