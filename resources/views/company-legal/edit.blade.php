@extends('layouts.master')
@section('page_title', 'Company Legal')
@section('content')

@section('content')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Company Legal</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('company-legals.update', $companyLegal->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('company-legal.form')

                        </form>
                    </div>
                </div>
@endsection
