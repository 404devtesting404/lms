@extends('layouts.master')
@section('page_title', 'Loan Modification')
@section('content')

@section('content')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Loan Modification</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('loan-modifications.update', $loanModification->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('loan-modification.form')

                        </form>
                    </div>
                </div>
@endsection
