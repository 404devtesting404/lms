@extends('layouts.master')
@section('page_title', 'My Dashboard')
@section('content')
    <div class="card">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-6 col-xl-6">
                        <div class="card card-body bg-warning-400 has-bg-image" style="background-color: #fd9843;">
                            <div class="media">
                                <div class="media-body">
                                    <h3 class="mb-0 db_text">Rs. {{ number_format($loan_disbursed) }}</h3>
                                    <span class="text-uppercase font-size-lg font-weight-bold">Total Disbursed</span>
                                </div>
                                <div class="mr-3 align-self-center">
                                    {{-- <i class="icon-coin-dollar icon-3x opacity-75"></i> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-6">
                        <div class="card card-body bg-warning-400 has-bg-image">
                            <div class="media">
                                <div class="media-body">
                                    <h3 class="mb-0 db_text">Rs.
                                        {{-- {{ number_format($outstanding) }} --}}
                                        {{ number_format($total_outstanding_sum) }} 
                                    </h3>
                                    <span class="text-uppercase font-size-lg font-weight-bold">Outstanding Portfolio</span>
                                </div>
                                <div class="ml-3 align-self-center">
                                    {{-- <i class="icon-coin-dollar icon-3x opacity-75"></i> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-6">
                        <div class="card card-body bg-danger-400 has-bg-image">
                            <div class="media">
                                <div class="media-body">
                                    <h3 class="mb-0 db_text">Rs. {{ number_format($profit) }}</h3>
                                    <span class="text-uppercase font-size-lg font-weight-bold">Principal Received</span>
                                </div>
                                <div class="mr-3 align-self-center">
                                    {{-- <i class="icon-coin-dollar  icon-3x opacity-75"></i> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-6">
                        <div class="card card-body bg-success-400 has-bg-image">
                            <div class="media">
                                <div class="media-body">
                                    <h3 class="mb-0 db_text">Rs. {{ number_format($total_profit) }}</h3>
                                    <span class="text-uppercase font-size-lg font-weight-bold"> Profit Received </span>
                                </div>
                                <div class="ml-3 align-self-center">
                                    {{-- <i class="icon-coin-dollar  icon-3x opacity-75"></i> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-6">
                        <div class="card card-body bd-yellow-400 has-bg-image" style="background-color: #ffcd39;">
                            <div class="media">
                                <div class="media-body">
                                    <h3 class="mb-0 db_text">Rs. {{ number_format($early_settlement_amount) }}</h3>
                                    <span class="text-uppercase font-size-lg font-weight-bold">Early Stellement</span>
                                </div>
                                <div class="ml-3 align-self-center">
                                    {{-- <i class="icon-coin-dollar  icon-3x opacity-75"></i> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-6">
                        <div class="card card-body bg-blue-400 has-bg-image">
                            <div class="media">
                                <div class="media-body">
                                    <h3 class="mb-0 db_text">Rs. {{ number_format($installment_recieved) }}</h3>
                                    <span class="text-uppercase font-size-lg font-weight-bold">Installment Received</span>
                                </div>
                                <div class="ml-3 align-self-center">
                                    {{-- <i class="icon-coin-dollar  icon-3x opacity-75"></i> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row justify-content-center"> <!-- Use Bootstrap class to center the child column -->
                    <div class="col-md-6 d-flex align-items-center justify-content-center" style="height: 100%;">
                        <!-- Additional CSS for vertical and horizontal centering -->
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <div class="card-header header-elements-inline">
            <h5 class="card-title">Events Calendar</h5>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <div class="fullcalendar-basic"></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ["Disbursed Cases", 'Early Settlement', 'Cancelled'],
                datasets: [{
                    backgroundColor: [
                        "#2ecc71",
                        "#FF7043",
                        "#ef5350",

                    ],
                    data: [{{ $loan_disbursed_count }}, {{ $early_settlement }}, {{ $loan_cancled }}],

                }]
            }
        });
    </script>
    {{-- Events Calendar Ends --}}
@endsection
