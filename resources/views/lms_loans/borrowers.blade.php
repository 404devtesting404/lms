@extends('layouts.master')
@section('page_title', 'Manage Borrowers')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Manage Borrowers</h6>
        {!! Qs::getPanelOptions() !!}


    </div>



    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#showall" class="nav-link active" data-toggle="tab">Borrowers</a></li>

        </ul>


        <div class="tab-content">



            <div class="tab-pane fade show active" id="showall">
                <table class="table datatable-button-html5-columns">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>CNIC</th>
                            <th>Mobile</th>
                            <th>DOB</th>
                            <th>Added On</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tt_records as $mc)
                        <tr>
                            <td>{{ $mc->id }}</td>
                            <td>{{ $mc->fname." ".$mc->mname." ".$mc->lname }}</td>
                            <td>{{ $mc->cnic }}</td>
                            <td>{{ $mc->mobile }}</td>
                            <td>{{ date("j M Y", strtotime($mc->dob)) }}</td>
                            <td>{{ date("j M Y H:i A", strtotime($mc->created_at)) }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="{{ route('ttr.showloan', $mc->id) }}">Show</a>
{{--                                        <a class="dropdown-item" href="{{ route('ttr.edit', $mc->id) }}">Edit</a>--}}
{{--                                        <a class="dropdown-item" href="{{ route('ttr.manage', $mc->id) }}">Manage</a>--}}
{{--                                        <a class="dropdown-item" id="{{ $mc->id }}" onclick="confirmDelete(this.id)" href="{{ route('ttr.destroy', $mc->id) }}">Delete</a>--}}
{{--                                    </div>--}}
                                </div>

{{--                                <div class="list-icons">--}}
{{--                                    <div class="dropdown">--}}
{{--                                        <a href="#" class="list-icons-item" data-toggle="dropdown">--}}
{{--                                            <i class="icon-menu9"></i>--}}
{{--                                        </a>--}}

{{--                                        <div class="dropdown-menu dropdown-menu-right">--}}

                                            <?php /*<a href="{{ route('ttr.show', $mc->id) }}" class="dropdown-item"><i class="icon-eye"></i> View</a>

                                            @if(Qs::userIsTeamSA())
                                            {{--Manage--}}
                                            <a href="{{ route('ttr.manage', $mc->id) }}" class="dropdown-item"><i class="icon-plus-circle2"></i> Manage</a>
                                            {{--Edit--}}
                                            <a href="{{ route('ttr.edit', $mc->id) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                            @endif

                                            {{--Delete--}}
                                            @if(Qs::userIsSuperAdmin())
                                            <a id="{{ $mc->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                            <form method="post" id="item-delete-{{ $mc->id }}" action="{{ route('ttr.destroy', $mc->id) }}" class="hidden">@csrf @method('delete')</form>
                                            @endif
*/ ?>

{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>

{{--TimeTable Ends--}}

@endsection
