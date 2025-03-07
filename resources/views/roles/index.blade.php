@extends('layouts.master')
@section('page_title', 'View Roles')
@section('content')

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable-button-html5-columns">
                    <thead class="thead">
                        <tr>
                            <th>No</th>
                            <th class="text-center">Role Name</th>
                            <th class="text-center">Created by</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i =1 ?>
                        @foreach ($roles as $val)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td class="text-center">{{ $val->role_name }}</td>
                                <td class="text-center">{{ $val->user->name }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" onclick="viewDataModal('roles/viewRolesDetail','<?php echo $val->id;?>','View Roles Detail')">View</a>
                                            <a class="dropdown-item" onclick="confirmDelete('<?=url('roles/delete?id='.$val->id)?>')">Delete</a>

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
