@extends('layouts.master')
@section('page_title', 'Manage Financing Details')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Process Financing Scheduling</h6>

            <div class="float-right">
                {{-- <a href="{{ route('loans.addloan') }}" class="btn btn-primary btn-sm float-right mb-1" data-placement="left"> --}}
                {{-- Add New Loan --}}
                {{-- </a> --}}

                <a href="{{ route('loans.payloans') }}" class="btn btn-success btn-sm float-right mb-1" data-placement="left">
                    Installment Posting
                </a>
            </div>


        </div>
        <div class="card-body">

            <div class="tab-content">



                <div class="tab-pane fade show active">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Product</th>
                                <!--<th>Office</th>-->
                                <th>Finance Amount</th>
                                <th>Outstanding</th>
                                <th>Disb.Date</th>
                                <th>Status</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalOutstanding = 0;
                            @endphp
                            @foreach ($tt_records as $mc)
                                <tr>
                                    <td><a href="{{ route('loans.menu', $mc->id) }}"
                                            class="c-anchor">{{ $loop->iteration }}</a></td>
                                    <td><a href="{{ route('loans.menu', $mc->id) }}"
                                            class="c-anchor">{{ @$mc->loan_borrower->fname . ' ' . @$mc->loan_borrower->mname . ' ' . @$mc->loan_borrower->lname }}</a>
                                    </td>
                                    <td>{{ $mc->loantype->name }}</td>
                                    <td>{{ number_format($mc->total_amount_pr + $mc->total_modification_amount, 0) }}</td>
                                    <!-- <td>{{ $mc->loan_office->name }}</td> -->
                                    <td>
                                        {{-- @if ($mc->last_outstanding > 0 && $mc->loan_status_id != 7)
                                            {{ number_format($mc->last_outstanding) }}
                                        @elseif($mc->loan_status_id == 7)
                                            0.00
                                        @else
                                            @php
                                                $LoanData = \App\Models\LoanHistory::where('id', $mc->id)
                                                    ->with('loan_borrower')
                                                    ->first();
                                            @endphp
                                            {{ number_format($LoanData->total_amount_pr) }}
                                        @endif --}}
                                        @php
                                            if ($mc->last_outstanding > 0 && $mc->loan_status_id != 7) {
                                                $outstanding = $mc->last_outstanding;
                                            } elseif ($mc->loan_status_id == 7) {
                                                $outstanding = 0;
                                            } else {
                                                $LoanData = \App\Models\LoanHistory::where('id', $mc->id)
                                                    ->with('loan_borrower')
                                                    ->first();
                                                $outstanding = $LoanData ? $LoanData->total_amount_pr : 0;
                                            }
                                            $totalOutstanding += $outstanding;
                                        @endphp
                                        {{ number_format($outstanding) }}
                                    </td>
                                    <td>{{ $mc->disb_date ? date('d M Y', strtotime($mc->disb_date)) : '-' }}</td>
                                    <td><?php echo \App\Helpers\Qs::getStatus($mc->loan_status_id); ?></td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn btn-outline-primary dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">

                                                Actions {{ $mc->loan_status_id }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="{{ route('ttr.showloan', $mc->id) }}"><i
                                                        class="icon-eye"></i>View Profile</a>
                                                <a class="dropdown-item" href="{{ route('loans.menu', $mc->id) }}"><i
                                                        class="icon-cash"></i> Generate Schedule</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- <h1><strong>{{ number_format($totalOutstanding) }}</strong></h1> --}}

            </div>
        </div>


        <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- TimeTable Ends --}}

@endsection
