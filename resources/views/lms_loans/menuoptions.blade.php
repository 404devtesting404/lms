@extends('layouts.master')
@section('page_title', 'Manage Financing')
@section('content')
    <style>
        .menuopt a {
            color: #FFFFFF !important;
            font-weight: bolder !important;


        }

        .menuopt .dropdown-item {
            color: #000000 !important;
            font-weight: bolder !important;

        }

        .labelinner {
            position: relative;
            /* margin-top: -10px; */
            /* margin-left: -15px; */
            /* margin-bottom: 30px; */
            /* background: darkorange; */
            color: lightcoral;
            /* padding: 2px 10px 2px 10px; */
            font-weight: 600;
            /*border-radius: 3px;*/
            text-decoration: underline;
        }

        .myTable td {
            width: 20%;
        }

        td strong {
            font-size: medium;
        }

        #dropdownMenuButton {
            font-weight: bolder !important;
        }

        .rightoptions {
            align-items: flex-start;
            justify-content: left;
            display: flex;
            width: 190px;
        }

        .rightoptions i {
            float: right;
            vertical-align: middle;
            position: absolute;
            right: 0px;
            margin: 10px;
        }
    </style>
    <div class="card">

        <!-- Enhancement Modal -->
        <div class="modal fade" id="enhanceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ $data->loantype->code == 'HC' || $data->loantype->code == 'HR' ? 'Trench' : 'Enhancement' }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if (!$LoanModification->isEmpty())
                            <div class="row">
                                <table border="1" width="100%">
                                    <tr>
                                        <th>Sr#</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>

                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($LoanModification as $row)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ number_format($row->amount, 0) }}</td>
                                            <td>{{ date('d M Y', strtotime($row->due_date)) }}</td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        @endif
                        <div class="row">
                            <form class="col-md-12" method="post" action="{{ route('enhancement') }}">
                                <input type="hidden" name="loanId" value="{{ $loanId }}" />
                                <input type="hidden" value="{{ csrf_token() }}" id="_token" name="_token" />
                                <div class="form-group">
                                    <label for="amount">Amount:</label>
                                    <input type="text" name="amount" id="amount" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="matdate">Enhancement Date:</label>
                                    <input type="date" name="enhdate" id="enhdate" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Proceed" class="btn btn-primary col-md-12" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Disbursement Modal -->
        <div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Rescheduling</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form class="col-md-12" method="post" action="{{ route('rescheduling') }}">
                                <input type="hidden" name="loanId" value="{{ $loanId }}" />
                                <input type="hidden" value="{{ csrf_token() }}" id="_token" name="_token" />

                                <div class="form-group">
                                    <label for="amount">Method:</label>
                                    <select name="method" class="form-control select">
                                        <option value="1">Increase</option>
                                        <option value="0">Decrease</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="matdate">Maturity Date:</label>
                                    <input type="date" name="matdate" id="matdate" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Proceed" class="btn btn-primary col-md-12" />
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Disbursement Modal -->
        <div class="modal fade" id="disburseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Disbursement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form class="col-md-12" method="post" action="{{ route('storestep') }}">
                                <input type="hidden" name="loanId" value="{{ $loanId }}" />
                                <input type="hidden" value="{{ csrf_token() }}" id="_token" name="_token" />
                                <div class="col-md-12">
                                    <label>Disbursement Date:</label>
                                    <input type="date" name="disb_date" placeholder="Disbursement Date"
                                        class="form-control mb-2" required />
                                    <label>Repayment Start Date:</label>
                                    <input type="date" name="rep_start_date" placeholder="Repayment Start Date"
                                        class="form-control mb-2" required />
                                    <label>Kibor:</label>
                                    <input type="text" name="kibor_rate" placeholder="Enter Kibor Rate"
                                        class="form-control mb-2" required />
                                    <label>Spread:</label>
                                    <input type="text" name="spread_rate" placeholder="Enter Spread Rate"
                                        class="form-control mb-2" required />
                                    @if ($data->loantype->id == 5 || $data->loantype->id == 6)
                                        <label for="islamic">Islamic</label>
                                        <input type="radio" name="is_islamic" value="1" id="islamic"
                                            class="mb-2">
                                        <label for="non-islamic">Non-Islamic</label>
                                        <input type="radio" name="is_islamic" value="0" id="non-islamic"
                                            class="mb-2">
                                    @endif

                                    <!-- UMI and EMI Report Option -->
                                    <label>Select Report Type:</label>
                                    <div class="mb-2">
                                        <input type="radio" id="umi" name="report_type" value="UMI"
                                            class="mr-1" required>
                                        <label for="umi">UMI Report</label>
                                    </div>
                                    <div class="mb-2">
                                        <input type="radio" id="emi" name="report_type" value="EMI"
                                            class="mr-1" required>
                                        <label for="emi">EMI Report</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary col-md-12 mb-2" type="submit">Disburse the borrower
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Kibor Tenure Modal -->
        <div class="modal fade" id="kiborTenureModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Kibor Tenure</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <select id="kibor_rev" class="col-md-12">
                                <option value="1">01 Year</option>
                                <option value="3">03 Year</option>
                                <option value="5">05 Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" <?php echo $data->loan_status_id == 10 ? 'disabled' : ''; ?>
                            onclick="saveKibor('kiborTenure')">
                            Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Processing Modal -->
        <div class="modal fade" id="processingfeesModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Processing Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if (isset($loanfees->processingFees))
                            <div class="row">
                                <table class="table table-bordered" width='100%'>
                                    <tr>
                                        <th>Company</th>
                                        <th>Fees</th>
                                    </tr>
                                    <tr>
                                        <td>AGFL</td>
                                        <td>{{ number_format($loanfees->processingFees, 0) }}</td>
                                    </tr>
                                </table>

                            </div>
                            <hr>
                        @endif
                        <div class="row">
                            <input class="form-control"
                                value="{{ isset($loanfees->processingFees) ? $loanfees->processingFees : '' }}"
                                type="number" placeholder="Enter Fees" id="processingfees" />
                        </div>
                        <div class="row form-check">
                            <input class="form-check-input" type="checkbox" value="" id="reverse">
                            <label class="form-check-label" for="reverse">
                                Reverse Entry
                            </label>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" <?php echo $data->loan_status_id == 10 ? 'disabled' : ''; ?> class="btn btn-primary"
                            onclick="saveFees('processingfees')">Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FED Modal -->
        <div class="modal fade" id="fedfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">FED Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if (isset($loanfees->fedFees))
                            <div class="row">
                                <table class="table table-bordered" width='100%'>
                                    <tr>
                                        <th>Company</th>
                                        <th>Fees</th>
                                    </tr>
                                    <tr>
                                        <td>FED</td>
                                        <td>{{ number_format($loanfees->fedFees, 0) }}</td>
                                    </tr>
                                </table>

                            </div>
                            <hr>
                        @endif


                        <div class="row">
                            <input type="number" value="{{ isset($loanfees->fedFees) ? $loanfees->fedFees : '' }}"
                                placeholder="Enter Fees" id="fedfees" />
                        </div>

                        <div class="row form-check">
                            <input class="form-check-input" type="checkbox" value="" id="reverse">
                            <label class="form-check-label" for="reverse">
                                Reverse Entry
                            </label>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" <?php echo $data->loan_status_id == 10 ? 'disabled' : ''; ?> class="btn btn-primary"
                            onclick="saveFees('fedfees')">Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Legal Modal -->
        <div class="modal fade" id="legalfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Legal Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class='row'>
                            <table class="table table-bordered" width='100%'>
                                <tr>
                                    <th>Company</th>
                                    <th>Fees</th>
                                </tr>
                                @foreach ($loanfeescomp as $row)
                                    @if ($row->FeesType == 'legalfees')
                                        <tr>
                                            <td>{{ isset($row->legalcompany) ? $row->legalcompany->name : '' }}</td>
                                            <td>{{ number_format($row->Fees, 0) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        </div>
                        <hr>
                        <div class="row">
                            <select class="select form-control" id="legalCompanyId">
                                <option value="">Select Legal Company</option>
                                @foreach ($legal as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <input class="form-control" type="number" placeholder="Enter Fees" id="legalfees" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" <?php echo $data->loan_status_id == 10 ? 'disabled' : ''; ?> class="btn btn-primary"
                            onclick="saveFees('legalfees')">Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Valuation Modal -->
        <div class="modal fade" id="valuationfeesModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Valuation Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class='row'>
                            <table class="table table-bordered" width='100%'>
                                <tr>
                                    <th>Company</th>
                                    <th>Fees</th>
                                </tr>
                                @foreach ($loanfeescomp as $row)
                                    @if ($row->FeesType == 'valuationfees')
                                        <tr>
                                            <td>{{ $row->valcompany->name }}</td>
                                            <td>{{ number_format($row->Fees, 0) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        </div>
                        <hr>

                        <div class="row">
                            <select class="select form-control" id="valuationCompanyId">
                                <option value="">Select Valuation Company</option>
                                @foreach ($valuation as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <input class="form-control" type="number" placeholder="Enter Fees" id="valuationfees" />
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" <?php echo $data->loan_status_id == 10 ? 'disabled' : ''; ?> class="btn btn-primary"
                            onclick="saveFees('valuationfees')">Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Income Modal -->
        <div class="modal fade" id="incomefeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Income Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class='row'>
                            <table class="table table-bordered" width='100%'>
                                <tr>
                                    <th>Company</th>
                                    <th>Fees</th>
                                </tr>
                                @foreach ($loanfeescomp as $row)
                                    @if ($row->FeesType == 'incomefees')
                                        <tr>
                                            <td>{{ $row->incomecompany->name }}</td>
                                            <td>{{ number_format($row->Fees, 0) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        </div>
                        <hr>

                        <div class="row">
                            <label>Income Estimation Company:</label>
                            <select class="select form-control" id="incomeEstCompanyId">
                                <option value="">Select Legal Company</option>
                                @foreach ($income as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <label>Income Estimation Amount:</label>
                            <input class="form-control" type="number" placeholder="Enter Fees" id="incomefees" />
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" <?php echo $data->loan_status_id == 10 ? 'disabled' : ''; ?> class="btn btn-primary"
                            onclick="saveFees('incomefees')">Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stamp Modal -->
        <div class="modal fade" id="stampfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Stamp Fees</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if (isset($loanfees->stampPaperFees))
                            <div class="row">
                                <table class="table table-bordered" width='100%'>
                                    <tr>
                                        <th>Company</th>
                                        <th>Fees</th>
                                    </tr>
                                    <tr>
                                        <td>Stamp Paper Fees</td>
                                        <td>{{ number_format($loanfees->stampPaperFees, 0) }}</td>
                                    </tr>
                                </table>

                            </div>
                            <hr>
                        @endif


                        <input type="number" placeholder="Enter Fees" id="stampfees"
                            value="{{ isset($loanfees->stampPaperFees) ? $loanfees->stampPaperFees : '' }}" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" <?php echo $data->loan_status_id == 10 ? 'disabled' : ''; ?> class="btn btn-primary"
                            onclick="saveFees('stampfees')">Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Lien Modal -->
        <div class="modal fade" id="lienfeesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Lien Amount</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        @if (isset($loanfees->lienFees))
                            <div class="row">
                                <table class="table table-bordered" width='100%'>
                                    <tr>
                                        <th>Company</th>
                                        <th>Fees</th>
                                    </tr>
                                    <tr>
                                        <td>AGFL</td>
                                        <td>{{ number_format($loanfees->lienFees, 0) }}</td>
                                    </tr>
                                </table>

                            </div>
                            <hr>
                        @endif


                        <input type="number" placeholder="Enter Amount" id="lienfees"
                            value="{{ isset($loanfees->lienFees) ? $loanfees->lienFees : '' }}" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" <?php echo $data->loan_status_id == 10 ? 'disabled' : ''; ?> class="btn btn-primary"
                            onclick="saveFees('lienfees')">Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="card-header header-elements-inline">

            <h6 class="card-title">Generate Financing Schedule</h6>
            {!! Qs::getPanelOptions() !!}

        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                @if (Qs::userIsTeamSA())
                    <li class="nav-item"><a href="#add-tt" class="nav-link active" data-toggle="tab">Create Schedule</a>
                    </li>
                @endif
            </ul>


            <div class="menuopt tab-content">
                <div class="row col-md-12">
                    <div class="col-md-2">

                        <div class="dropdown">
                            <button class="btn btn-success col-md-12 dropdown-toggle" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Legal Documents
                            </button>
                            <div class="dropdown-menu success" aria-labelledby="dropdownMenuButton">
                                @foreach ($files as $key => $file)
                                    <a class="dropdown-item"
                                        href="{{ route('ttr.legal_docs', ['loanId' => $loanId, 'doc_id' => $key, 'loantypeid' => $data->loan_type_id]) }}">{{ ++$key }}
                                        : {{ $file }}.docx</a>
                                @endforeach
                            </div>
                        </div>

                        <!--<a href="{{ url('/assets/legalDocuments.zip') }}" class="btn btn-success col-md-12">Legal Documents</a>-->
                        <!--                    <a href="{{ route('ttr.legal_docs', $loanId) }}" class="btn btn-success col-md-12">Legal Documents</a>-->
                        <!--                    </div>
                                                <div class="col-md-2">
                                                    <a href="{{ route('ttr.show_schedule', $loanId) }}" class="btn btn-success col-md-12">Repayment Schedule</a>
                                                </div>
                                                <div class="col-md-2">
                                                    <a href="{{ route('loans.takafulreport', $loanId) }}" class="btn btn-success col-md-12">Takaful Policy</a>
                                                </div>
                                                <div class="col-md-2">
                                                    <a href="{{ route('loans.pay', $loanId) }}" class="btn btn-success col-md-12">Pay Installment</a>
                                                </div>
                                                <div class="col-md-2">
                                                    <a href="{{ route('loans.partial', $loanId) }}" class="btn btn-success col-md-12">Partial Payment</a>
                                                </div>
                                                <div class="col-md-2">
                                                    <a href="{{ route('loans.early', $loanId) }}" class="btn btn-success col-md-12">Early Settlement</a>
                                                </div>
                            -->


                        <!--<a href="{{ url('/assets/legalDocuments.zip') }}" class="btn btn-success col-md-12">Legal Documents</a>-->
                        <!--                    <a href="{{ route('ttr.legal_docs', $loanId) }}" class="btn btn-success col-md-12">Legal Documents</a>-->
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('ttr.show_schedule', $loanId) }}" class="btn btn-success col-md-12">Repayment
                            Schedule</a>
                    </div>
                    <div class="col-md-2">
                        <!--<a href="{{ route('loans.takafulreport', $loanId) }}" class="btn btn-success col-md-12">Takaful Policy</a>-->
                        <div class="dropdown">
                            <button class="btn btn-success col-md-12 dropdown-toggle" type="button"
                                id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Takaful Policy
                            </button>
                            <div class="dropdown-menu success" aria-labelledby="dropdownMenuButton2">
                                <a class="dropdown-item"
                                    href="{{ route('loans.takafulreport', ['loanId' => $loanId, 'type' => 1]) }}">Life
                                    Takaful</a>
                                <a class="dropdown-item"
                                    href="{{ route('loans.takafulreport', ['loanId' => $loanId, 'type' => 0]) }}">Property
                                    Takaful</a>
                            </div>
                        </div>


                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('loans.pay', $loanId) }}" class="btn btn-success col-md-12">Pay
                            Installment</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('loans.partial', $loanId) }}" class="btn btn-success col-md-12">Partial
                            Payment</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('loans.early', $loanId) }}" class="btn btn-success col-md-12">Early
                            Settlement</a>
                    </div>
                </div>
                <br>
                <br>
                <div class="row col-md-12">
                    <table class="myTable table col-md-12" border="1" cellpadding="5" cellspacing="0">
                        <tr>
                            <td>
                                <span class="labelinner">Product Name:</span> <br>
                                <strong>{{ $data->loantype->name }}
                                    <br>{{ $data->sanction_number ? $data->sanction_number : '' }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">LOS Customer
                                    ID:</span><br><strong>{{ $data->los_app_id }}</strong>
                                <input type="hidden" value="{{ $loanId }}" id="loan_id" />
                                <input type="hidden" value="{{ csrf_token() }}" id="_token" />
                            </td>
                            <td>
                                <span class="labelinner">Customer
                                    Name:</span><br><strong>{{ $data->loan_borrower->fname . ' ' . $data->loan_borrower->mname . ' ' . $data->loan_borrower->lname }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">CNIC:</span><br><strong>{{ $data->loan_borrower->cnic }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">Property Purchase
                                    Amount:</span><br><strong>{{ number_format($data->property_amount, 0) }}</strong>
                            </td>
                            <td width="20%" rowspan="7">
                                <div></div>
                                <a class="rightoptions btn btn-primary col-md-12 mb-1" data-toggle="modal"
                                    {!! $data->loan_status_id == 9 ? '' : 'data-target="#kiborTenureModal"' !!}>Kibor
                                    Tenure {!! isset($data->kibor_revision_cycle) && $data->kibor_revision_cycle
                                        ? "&nbsp;<i class='icon-checkmark' ></i>"
                                        : '' !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal"
                                    data-target="#processingfeesModal">Processing
                                    Fees {!! isset($loanfees->processingFeesStatus) && $loanfees->processingFeesStatus
                                        ? "&nbsp;<i class='icon-checkmark' ></i>"
                                        : '' !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal"
                                    data-target="#fedfeesModal">FED
                                    Amount{!! isset($loanfees->fedFeesStatus) && $loanfees->fedFeesStatus ? "&nbsp;<i class='icon-checkmark' ></i>" : '' !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal"
                                    data-target="#legalfeesModal">Legal
                                    Company{!! isset($loanfees->legalFeesStatus) && $loanfees->legalFeesStatus
                                        ? "&nbsp;<i class='icon-checkmark' ></i>"
                                        : '' !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal"
                                    data-target="#valuationfeesModal">Valuation
                                    Company{!! isset($loanfees->valuationFeesStatus) && $loanfees->valuationFeesStatus
                                        ? "&nbsp;<i class='icon-checkmark' ></i>"
                                        : '' !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal"
                                    data-target="#incomefeesModal">Income Est.
                                    Company{!! isset($loanfees->incomeEstFeesStatus) && $loanfees->incomeEstFeesStatus
                                        ? "&nbsp;<i class='icon-checkmark' ></i>"
                                        : '' !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal"
                                    data-target="#stampfeesModal">Stamp Paper
                                    Charges{!! isset($loanfees->stampPaperFeesStatus) && $loanfees->stampPaperFeesStatus
                                        ? "&nbsp;<i class='icon-checkmark' ></i>"
                                        : '' !!}</a>
                                <a class="rightoptions btn btn-secondary col-md-12 mb-1" data-toggle="modal"
                                    data-target="#lienfeesModal">Lien
                                    Amount{!! isset($loanfees->lienFeesStatus) && $loanfees->lienFeesStatus ? "&nbsp;<i class='icon-checkmark' ></i>" : '' !!}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="labelinner">Finance
                                    Amount:</span><br><strong>{{ number_format($data->total_amount_pr, 0) }}
                                    <br>{{ $modAmount ? 'Enhancement: ' . number_format($modAmount, 0) : '' }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">Loan Account
                                    Number:</span><br><strong>{{ $data->account_no ? $data->account_no : '' }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">Tenure in
                                    Years:</span><br><strong>{{ $data->loan_period / 12 }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">Tenure in
                                    Months:</span><br><strong>{{ $data->loan_period }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">Customer
                                    Equity:</span><br><strong>{{ number_format($data->property_amount - $data->total_amount_pr, 0) }}</strong>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <span class="labelinner">Musharakah
                                    Date:</span><br><strong>{{ $data->musharakah_date ? date('j M, Y', strtotime($data->musharakah_date)) : '' }}</strong>
                            </td>
                            <td><span class="labelinner">Disbursement Date:</span><br>
                                <strong>{{ date('j M, Y', strtotime($data->disb_date)) }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">1st Installment
                                    Date:</span><br><strong>{{ $data->due->ins_date ? date('j M, Y', strtotime($data->due->ins_date)) : '' }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">Maturity
                                    Date:</span><br><strong>{{ $data->due->mat_date ? date('j M, Y', strtotime($data->due->mat_date)) : '' }}</strong>
                            </td>
                            <td>
                                <span class="labelinner">AGFL
                                    Finance:</span><br><strong>{{ number_format($data->total_amount_pr, 0) }}</strong>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <span
                                    class="labelinner">Kibor:</span><br><strong>{{ number_format($data->kibor_rate, 2) }}
                                    %</strong>
                            </td>
                            <td>
                                <span
                                    class="labelinner">Spread:</span><br><strong>{{ number_format($data->spread_rate, 2) }}
                                    %</strong>
                            </td>
                            <td>
                                <span class="labelinner">Total
                                    Rate:</span><br><strong>{{ number_format($data->spread_rate + $data->kibor_rate, 2) }}
                                    %</strong>
                            </td>

                            <td><span class="labelinner">Renewal of Kibor:</span><br>
                                <strong>
                                    @if ($data->loan_status_id == 10)
                                        <a class="col-md-12" style="color: #2196f3 !important;"
                                            href="{{ route('kiborrenewalschedule', $loanId) }}">
                                            {{ $data->kibor_revision_cycle ? ($data->kibor_revision_cycle == 1 ? $data->kibor_revision_cycle . ' Year' : $data->kibor_revision_cycle . ' Years') : 'Not Set' }}
                                        </a>
                                    @else
                                        {{ $data->kibor_revision_cycle ? ($data->kibor_revision_cycle == 1 ? $data->kibor_revision_cycle . ' Year' : $data->kibor_revision_cycle . ' Years') : 'Not Set' }}
                                    @endif
                                </strong>
                                <input type="hidden" id="loan_id" value="{{ $loanId }}" />
                            </td>
                            <td>
                                <span class="labelinner">Reschedule Kibor
                                    Date:</span><br><strong>{{ date('j M, Y', strtotime($data->kibor_revision_date)) }}</strong>
                            </td>
                        </tr>
                    </table>
                </div>
                <br><br>
                <div class="row col-md-12">

                    <div class="col-md-2">
                        <a href="{{ route('ttr.legal_docs', ['loanId' => $loanId, 'doc_id' => 'ack', 'loantypeid' => $data->loan_type_id]) }}"
                            class=".dropdown-item btn btn-success col-md-12">Acknowledgement</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('ttr.legal_docs', ['loanId' => $loanId, 'doc_id' => 'wel', 'loantypeid' => $data->loan_type_id]) }}"
                            class=".dropdown-item btn btn-success col-md-12">Welcome Letter</a>

                    </div>

                    <div class="col-md-2">
                        <a class="btn btn-success col-md-12" href="{{ route('kiborrenewalschedule', $loanId) }}">Renewal
                            Kibor Schedule</a>
                        <!--<a class="btn btn-success col-md-12" href="{{ route('runkibor', $loanId) }}">Renewal Kibor Schedule</a>-->
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('ttr.legal_docs', ['loanId' => $loanId, 'doc_id' => 'tax', 'loantypeid' => $data->loan_type_id]) }}"
                            class=".dropdown-item btn btn-success col-md-12">Tax Certificate</a>
                    </div>
                    <div class="col-md-2">
                        <!--<a href="{{ route('rescheduling', $loanId) }}" class="btn btn-success col-md-12">Tenure Rescheduling</a>-->
                        <!--//route('rescheduling', $loanId)-->
                        <a class="btn btn-success col-md-12" data-toggle="modal" data-target="#rescheduleModal">Tenure
                            Rescheduling</a>


                    </div>
                    <div class="col-md-2">
                        <!--//route('enhancement', $loanId)-->
                        <a class="btn btn-success col-md-12" data-toggle="modal"
                            data-target="#enhanceModal">{{ $data->loantype->code == 'HC' || $data->loantype->code == 'HR' ? 'Trench' : 'Enhancement' }}</a>
                    </div>
                    <br>
                    <br>
                    <br>
                </div>
                <div class="row" <?php echo $data->loan_status_id == 10 ? "style='display:none;'" : ''; ?>>

                    <div class="col-md-12">
                        <!--                        <a id="storestep" href="{{ route('storestep', $loanId) }}" class="btn btn-primary col-md-12  mb-2" >Disburse the borrower</a>-->
                        <a class="btn btn-primary col-md-12 mb-2" data-toggle="modal"
                            data-target="#disburseModal">Disburse
                            the borrower</a>
                    </div>
                </div>

            </div>
        </div>

        <script>
            function disburse() {
                $(".ui-pnotify").children(".ui-pnotify-text").html("Hello Teerath");
                $(".ui-pnotify").show();
                //href="{{ route('storestep', $loanId) }}"

                //ui-pnotify-text
                //ui-pnotify
                //onclick="disburse()"
                //        var kibor_rev_tenure = $("#kibor_rev").val();
                //        var loanId = $("#loan_id").val();
                //        if(kibor_rev_tenure!=0){
                //            var payload = {"_token": $("#_token").val(), loanId: $("#loan_id").val(), kibor_rev: kibor_rev_tenure};
                //            $.post("/storestep", payload, function (data) {
                //    //            alert(data);
                //                if (data.success == true) {
                //                    alert("Successfully Saved");
                //                    $("#" + id + "Modal").modal('toggle');
                //                    location.reload();
                //                }
                //            });
                //
                //        } else {
                //            alert("Please select kibor revision tenure");
                //        }
            }


            function saveFees(id) {
                var valueof = $("#" + id).val();
                if (valueof == "") {
                    alert("Fees must not be blank");
                    return;
                }
                //        alert(id);
                if (id == "incomefees")
                    var payload = {
                        "_token": $("#_token").val(),
                        loan_id: $("#loan_id").val(),
                        company_id: $("#incomeEstCompanyId").val(),
                        fieldkey: id,
                        fieldvalue: $("#" + id).val()
                    };
                else if (id == "valuationfees")
                    var payload = {
                        "_token": $("#_token").val(),
                        loan_id: $("#loan_id").val(),
                        company_id: $("#valuationCompanyId").val(),
                        fieldkey: id,
                        fieldvalue: $("#" + id).val()
                    };
                else if (id == "legalfees")
                    var payload = {
                        "_token": $("#_token").val(),
                        loan_id: $("#loan_id").val(),
                        company_id: $("#legalCompanyId").val(),
                        fieldkey: id,
                        fieldvalue: $("#" + id).val()
                    };
                else if (id == "fedfees") {
                    var reverse = $("#reverse").is(':checked');
                    //        if (reverse == true) {
                    //        $("#" + id).val(0);
                    //        }
                    //            alert("Checked: " + reverse);
                    var payload = {
                        "_token": $("#_token").val(),
                        loan_id: $("#loan_id").val(),
                        fieldkey: id,
                        fieldvalue: $("#" + id).val(),
                        rev: reverse
                    };
                } else if (id == "processingfees") {
                    var reverse = $("#reverse").is(':checked');
                    //        if (reverse == true) {
                    //        $("#" + id).val(0);
                    //        }
                    //            alert("Checked: " + reverse);
                    var payload = {
                        "_token": $("#_token").val(),
                        loan_id: $("#loan_id").val(),
                        fieldkey: id,
                        fieldvalue: $("#" + id).val(),
                        rev: reverse
                    };
                } else
                    var payload = {
                        "_token": $("#_token").val(),
                        loan_id: $("#loan_id").val(),
                        fieldkey: id,
                        fieldvalue: $("#" + id).val()
                    };
                $.post("{{ route('savefeesdata') }}", payload, function(data) {
                    //            alert(data);
                    if (data.success == true) {
                        alert("Successfully Saved");
                        $("#" + id + "Modal").modal('toggle');
                        location.reload();
                    } else {
                        alert("Unable to update because case is already disbursed");
                    }
                });
            }

            function saveKibor(id) {
                var payload = {
                    "_token": $("#_token").val(),
                    loanId: $("#loan_id").val(),
                    kibor_rev: $("#kibor_rev").val()
                };
                $.post("/loans/storestepkibor", payload, function(data) {
                    if (data.success == true) {
                        alert("Successfully Saved");
                        $("#" + id + "Modal").modal('toggle');
                        location.reload();
                    }
                });
            }
        </script>
        {{-- TimeTable Ends --}}

    @endsection
