@extends('layouts.master')
@section('page_title', 'Loans Fee')
@section('content')

    <div class="card card-default">
        <div class="card-header">
            <span class="card-title">Create Loans Fee</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('loans-fees.store') }}"  role="form" enctype="multipart/form-data">
                @csrf

                @include('loans-fee.form')

            </form>
        </div>
    </div>

@endsection
