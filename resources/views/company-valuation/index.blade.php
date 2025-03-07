@extends('layouts.master')
@section('page_title', 'Company Valuation')
@section('content')

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Company Valuation') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('company-valuations.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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

                                    @foreach ($companyValuations as $companyValuation)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $companyValuation->name }}</td>
											<td>{{ $companyValuation->status }}</td>

                                            <td>
                                                <form action="{{ route('company-valuations.destroy',$companyValuation->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('company-valuations.show',$companyValuation->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('company-valuations.edit',$companyValuation->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
