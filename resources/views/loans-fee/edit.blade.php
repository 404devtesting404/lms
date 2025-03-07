@extends('layouts.master')
@section('page_title', 'Loans Fee')
@section('content')

@section('content')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Loans Fee</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('loans-fees.update', $loansFee->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('loans-fee.form')

                        </form>
                    </div>
                </div>
@endsection
