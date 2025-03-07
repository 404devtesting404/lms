@extends('layouts.master')
@section('page_title', 'Company Income Est')
@section('content')

@section('content')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Company Income Est</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('company-income-ests.update', $companyIncomeEst->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('company-income-est.form')

                        </form>
                    </div>
                </div>
@endsection
