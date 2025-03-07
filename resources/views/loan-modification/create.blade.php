@extends('layouts.master')
@section('page_title', 'Loan Modification')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Loan Modification</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('loan-modifications.store') }}"  role="form" enctype="multipart/form-data">
                @csrf

                @include('loan-modification.form')

            </form>
        </div>
    </div>

@endsection
