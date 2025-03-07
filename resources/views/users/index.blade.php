@extends('layouts.master')
@section('page_title', 'Users List')
@section('content')

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable-button-html5-columns">
                    <thead class="thead">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created on</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=0; ?>
                        @foreach ($users as $val)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $val->name }}</td>
                                <td>{{ $val->email }}</td>
                                <td>{{ $val->role->role_name }}</td>
                                <td>{{ \App\Helpers\Qs::dateFormat($val->created_at) }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="{{url('users/edit?id='.$val->id)}}">Edit</a>
                                            <a class="dropdown-item" onclick="confirmDelete('<?=url('users/delete?id='.$val->id)?>')">Delete</a>

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
