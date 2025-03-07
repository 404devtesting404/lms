@extends('layouts.master')
@section('page_title', 'Authorization Process')
@section('content')

<div class="card">
    <div class="card-body">

        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#auth" class="nav-link active" data-toggle="tab">Authorize Disbursement</a></li>
            <li class="nav-item"><a href="#other" class="nav-link" data-toggle="tab">Authorize Other</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade" id="auth">

                <div class="table-responsive">

                    <table class="table datatable-button-html5-columns">
                        <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>Account#</th>
                                <th>Borrower</th>
                                <th>Disbursed Amount</th>
                                <th>Requested Date</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $paidinfo = [];
                            ?>
                            @foreach($paidinfo as $mc)

                            @if($mc->due_status==0)
                            <?php $mc->amount_pr = (isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2])) ? $mc->amount_pr + $duepaidinfo[2][$mc->id]->amount_pr : $mc->amount_pr ?>
                            <?php $mc->outstanding = (isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2])) ? $mc->outstanding - $duepaidinfo[2][$mc->id]->amount_pr : $mc->outstanding ?>
                            <tr>
                                <td>
                                    <?php echo $i++ ?>

                                </td>
                                <td>{{ date("d M Y",strtotime($mc->due_date)) }}</td>
                                <td>{{ number_format(((int)$mc->amount_pr),0) }}</td>
                                <td>{{ number_format(((int)$mc->amount_mu),0) }}</td>
                                <td>{{ number_format(((int)$mc->amount_total),0) }}</td>
                                <td>{{ number_format(((int)$mc->outstanding)) }}</td>
                                <td>
                                    <?php
                                    echo (isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2])) ? '<span class="badge badge-pill badge-success">PARTIAL</span>' : "";
                                    echo (isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2])) ? '<span class="badge badge-pill badge-success">PARTIAL</span>' : "";
                                    ?>
                                </td>
                                <th>{{ date("j M Y", strtotime($mc->recovered_date)) }}</th>
                                <th>{{ $mc->od_days ? $mc->od_days : "0" }}</th>
                                <th>{{ $mc->od_days && $mc->od_days>7 ? $mc->od_days*100 : "-" }}</th>

                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>



            <div class="tab-pane fade show active" id="other">   

                <div class="table-responsive">

                    <table class="table datatable-button-html5-columns">
                        <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>Account#</th>
                                <th>Borrower</th>
                                <th>Disbursed Amount</th>
                                <th>Requested Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $dueinfo = [];
                            ?>
                            @foreach($dueinfo as $mc)

                            @if($mc->due_status==0)
                            <?php $mc->amount_pr = (isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2])) ? $mc->amount_pr + $duepaidinfo[2][$mc->id]->amount_pr : $mc->amount_pr ?>
                            <?php $mc->outstanding = (isset($duepaidinfo[2]) && array_key_exists($mc->id, $duepaidinfo[2])) ? $mc->outstanding - $duepaidinfo[2][$mc->id]->amount_pr : $mc->outstanding ?>


                            <tr>

                                <td>
                                    <?php echo $i++ ?>
                                </td>
                                <td>{{ date("d M Y",strtotime($mc->due_date)) }}</td>
                                <td>{{ number_format($mc->amount_pr,0) }}</td>
                                <td>{{ number_format($mc->amount_mu,0) }}</td>
                                <td>{{ number_format($mc->amount_total,0) }}</td>
                                <td>{{ $mc->enhancement_amount>0 ? number_format(($mc->outstanding+$mc->enhancement_amount),0) : number_format(($mc->outstanding),0) }}</td>
                                <td align="center">
                                    <?php
                                    echo ($mc->partial > 0) ? '<span class="badge badge-pill badge-primary" style="padding-right: 0px !important; padding-left: 0px !important;">' . (number_format($mc->partial, 0)) . ' (Partial)</span>' : "";
                                    echo ($mc->enhancement_amount > 0) ? '<span class="badge badge-pill badge-primary" style="padding-right: 0px !important; padding-left: 0px !important;">' . (number_format($mc->enhancement_amount, 0)) . ' (Enhancement)</span>' : "";
                                    ?></td>
                                <td><?php echo $mc->payment_status ? '<span class="badge badge-pill badge-success">&nbsp;&nbsp;&nbsp;&nbsp;PAID&nbsp;&nbsp;&nbsp;&nbsp;</span>' : '<span class="badge badge-pill badge-danger">UN-PAID</span>' ?></td>
                            </tr>
                            @endif
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
@endsection
