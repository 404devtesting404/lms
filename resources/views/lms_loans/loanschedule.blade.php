@extends('layouts.master')
@section('page_title', 'Financing Schedule')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Repayments Schedule of
                <strong>{{ $loaninfo->loan_borrower->fname . ' ' . $loaninfo->loan_borrower->mname . ' ' . $loaninfo->loan_borrower->lname }}</strong>
                (A/c#: {{ $ttr_id }})</h6>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">

            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#due" class="nav-link active" data-toggle="tab">Due Schedule</a></li>
                <li class="nav-item"><a href="#paid" class="nav-link" data-toggle="tab">Paid Installment</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade" id="paid">

                    <div class="table-responsive">

                        <table class="table datatable-button-html5-columns">
                            <thead>
                                <tr>
                                    <th>Inst.No</th>

                                    <th>Due Date</th>
                                    <th>Principal</th>
                                    <th>Profit</th>
                                    <th>Installment Amount</th>
                                    <th>Outstanding Principle</th>
                                    <th>Desc.</th>
                                    <th>Inst. Pay Date</th>
                                    <th>OverDue Days</th>
                                    <th>Charity Amount</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($paidinfo as $mc)
                                    @if ($mc->due_status == 0)
                                        <?php $mc->amount_pr = isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2]) ? $mc->amount_pr + $duepaidinfo[2][$mc->id]->amount_pr : $mc->amount_pr; ?>
                                        <?php $mc->outstanding = isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2]) ? $mc->outstanding - $duepaidinfo[2][$mc->id]->amount_pr : $mc->outstanding; ?>
                                        <tr>
                                            <td>
                                                <?php echo $i++; ?>

                                            </td>
                                            <td>{{ !empty($mc->due_date) ? date('d M Y', strtotime($mc->due_date)) : date('d M Y', strtotime($mc->recovered_date)) }}
                                            </td>
                                            <td>{{ number_format(((int) $mc->amount_pr), 0) }}</td>
                                            <td>{{ number_format(((int) $mc->amount_mu), 0) }}</td>
                                            <td>{{ number_format(((int) $mc->amount_total), 0) }}</td>
                                            <td>{{ number_format((int) $mc->outstanding) }}</td>

                                            <td>
                                                <?php
                                                echo isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2]) ? '<span class="badge badge-pill badge-success">PARTIAL</span>' : '';
                                                echo isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2]) ? '<span class="badge badge-pill badge-success">PARTIAL</span>' : '';
                                                ?>
                                            </td>
                                            <th>{{ date('j M Y', strtotime($mc->recovered_date)) }}</th>
                                            <th>{{ $mc->od_days ? $mc->od_days : '0' }}</th>
                                            <th>{{ $mc->od_days && $mc->od_days > 7 ? $mc->od_days * 100 : '-' }}</th>

                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!--<div class="table-responsive">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                                <tr>
                                    <th>Inst.No</th>

                                    <th>Due Date</th>
                                    <th>Principal</th>
                                    <th>Profit</th>
                                    <th>Installment Amount</th>
                                    <th>Outstanding Principle</th>
                                    <th>Desc.</th>
                                    <th>Payment Status</th>

                                    <th>Due ID</th>
                                    <th>Principal</th>
                                    <th>Profit</th>
                                    <th>Total</th>
                                    <th>Paid Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paidinfo as $mc)
    <tr>
                                    <td>{{ $mc->due_id }}</td>
                                    <td align="right">{{ number_format($mc->amount_pr) }}</td>
                                    <td align="right">{{ number_format($mc->amount_mu) }}</td>
                                    <td align="right">{{ number_format($mc->amount_total) }}</td>
                                    <td>{{ date('d M Y', strtotime($mc->recovered_date)) }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
    @endforeach
                            </tbody>
                        </table>
                    </div>-->
                </div>
                <div class="tab-pane fade show active" id="due">
                    <a onclick="return confirm('Are you sure to reverse?')"
                        href="{{ route('loans.reverse_pay', $ttr_id) }}" class="btn btn-dark col-md-2 float-right">Reverse
                        Payment</a>
                    <div class="table-responsive">

                        <table class="table datatable-button-html5-columns">
                            <thead>
                                <tr>
                                    <th>Inst.No</th>
                                    <th>ID</th>

                                    <th>Due Date</th>
                                    <th>Principal</th>
                                    <th>Profit</th>
                                    <th>Installment Amount</th>
                                    <th>Outstanding Principle</th>
                                    <th>Charity Amount</th>
                                    <th>Desc.</th>
                                    <th>Payment Status</th>
                                    <th>Settle Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($dueinfo as $mc)
                                    {{-- @if ($mc->due_status == 0)  --}}
                                    <?php $mc->amount_pr = isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2]) ? $mc->amount_pr + $duepaidinfo[2][$mc->id]->amount_pr : $mc->amount_pr; ?>
                                    <?php $mc->outstanding = isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2]) ? $mc->outstanding - $duepaidinfo[2][$mc->id]->amount_pr : $mc->outstanding; ?>
                                    @php
                                        //$mc->amount_pr = (int)$mc->amount_pr;
                                        //$mc->amount_mu = (int)$mc->amount_mu;
                                        //$mc->amount_total = (int)$mc->amount_total;
                                    @endphp
                                    <tr>
                                        <td>
                                            <?php echo $i++; ?>
                                        </td>
                                        <td>
                                            {{ $mc->id }}
                                        </td>

                                        <td>{{ date('d M Y', strtotime($mc->due_date)) }}</td>
                                        <td>{{ number_format($mc->amount_pr, 0) }}</td>
                                        <td>{{ number_format($mc->amount_mu, 0) }}</td>
                                        <td>{{ number_format($mc->amount_total, 0) }}</td>
                                        <td>{{ $mc->enhancement_amount > 0 ? number_format($mc->outstanding + $mc->enhancement_amount, 0) : number_format($mc->outstanding, 0) }}
                                        </td>
                                        <td>{{ number_format($mc->charity_amount) }}</td>
                                        {{-- <td></td> --}}
                                        <td align="center">
                                            <?php
                                            // echo '<span class="badge badge-pill badge-primary" style="padding-right: 0px !important; padding-left: 0px !important;">' . number_format($mc->partial, 0) . ' (Partial)</span>';
                                            // echo '<span class="badge badge-pill badge-primary" style="padding-right: 0px !important; padding-left: 0px !important;">' . number_format($mc->enhancement_amount, 0) . ' (Enhancement)</span>';
                                            echo $mc->partial > 0 ? '<span class="badge badge-pill badge-primary" style="padding-right: 0px !important; padding-left: 0px !important;">' . number_format($mc->partial, 0) . ' (Partial)</span>' : '';
                                            echo $mc->enhancement_amount > 0 ? '<span class="badge badge-pill badge-primary" style="padding-right: 0px !important; padding-left: 0px !important;">' . number_format($mc->enhancement_amount, 0) . ' (Enhancement)</span>' : '';
                                            ?>
                                        </td>
                                        <td>
                                            @if ($mc->payment_status == 1)
                                                {{ $mc->payment_status }} one
                                                <span
                                                    class="badge badge-pill badge-success">&nbsp;&nbsp;&nbsp;&nbsp;PAID&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                            @elseif($mc->payment_status == 7)
                                                {{ $mc->payment_status }} seven

                                                <span class="badge badge-pill badge-success">&nbsp;&nbsp;&nbsp;&nbsp;Early
                                                    Settlement&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                            @else
                                                {{ $mc->payment_status }} eles
                                                <span class="badge badge-pill badge-danger">UN-PAID</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($mc->is_settle == 1)
                                                <span class="badge badge-pill badge-success">&nbsp;&nbsp;&nbsp;&nbsp;Early
                                                    Settlement&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                {{-- @endif --}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
            </div>
        </div>
    </div>

    {{-- TimeTable Ends --}}

@endsection
