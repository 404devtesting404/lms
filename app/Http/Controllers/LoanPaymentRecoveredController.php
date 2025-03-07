<?php

namespace App\Http\Controllers;

use App\Models\LoanHistory;
use Carbon\Carbon;
use DateTime;
use App\Models\LoanPaymentRecovered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LoanPaymentDue;

/**
 * Class LoanPaymentRecoveredController
 * @package App\Http\Controllers
 */
class LoanPaymentRecoveredController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rev_payment(Request $request)
    {

        dd($request);
    }
    public function pay_installment($loanId)
    {

        // amount_total amount_pr amount_mu
        $TotalAmountDue = \App\Models\LoanPaymentDue::where(
            ['loan_id' => $loanId, 'payment_status' => 0, 'due_status' => 0, ['due_date', '<=', date('Y-m-d')]]
        )->sum('amount_total');
        $MinDueDate = \App\Models\LoanPaymentDue::where(
            ['loan_id' => $loanId, 'payment_status' => 0, 'due_status' => 0]
        )->min('due_date');

        $TotalAmountDue = $TotalAmountDue ? $TotalAmountDue : 0;

        $AmountInstallment = \App\Models\LoanPaymentDue::where(
            ['loan_id' => $loanId, 'payment_status' => 0]
        )->first('amount_total');
        $AmountInstallment = $AmountInstallment ? $AmountInstallment->amount_total : 0;
        //dd($AmountInstallment);
        $loanPaymentRecovered = new LoanPaymentRecovered();
        //echo "Here it is";
        //dd($TotalAmountDue);


        //        $DDLList = [];
        //        if (\App\Models\LoanBankslip::where('slipDate', date('Y-m-d'))->count() <= 0) {
        //            //echo "Is it here";
        //            \App\Models\LoanBankslip::create(['slipDate' => date('Y-m-d'), 'name' => 'Manual Repayments']);
        //            $BankSlips = \App\Models\LoanBankslip::where('slipDate', date('Y-m-d'))->get();
        //        } else {
        //            $BankSlips = \App\Models\LoanBankslip::where('slipDate', date('Y-m-d'))->get();
        //        }
        //        //dd($BankSlips);
        //        foreach ($BankSlips as $BankSlips) {
        //            $DDLList[$BankSlips->id] = $BankSlips->name . ' ' . $BankSlips->slipDate;
        //        }


        return view('loan-payment-recovered.pay', compact('loanPaymentRecovered'))->with(['loanId' => $loanId, 'TotalAmountDue' => $TotalAmountDue, 'AmountInstallment' => $AmountInstallment, 'MinDueDate' => $MinDueDate]);
    }

    public function partialPayment($loanId)
    {
        $LoansInfo = \App\Models\LoanHistory::where('id', $loanId)->first();

        $OutstandingData = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();
        if (!$OutstandingData) {
            $OutstandingData = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 0])->first();
            $outstanding = $LoansInfo->total_amount_pr;
        } else {
            $outstanding = $OutstandingData->outstanding;
        }
        $partialPercentage = 15 / 100;
    }

    public function pay_ealrysettlement($loanId)
    {
        $LoansInfo = \App\Models\LoanHistory::where('id', $loanId)->first();
        $loan_recovered = \App\Models\LoanPaymentRecovered::where(['loan_id' => $loanId, 'is_settle' => 1])->first();

        $early_settle_date = $loan_recovered ? date('Y-m-d', strtotime($loan_recovered->recovered_date)) : date('Y-m-d');

        $OutstandingData = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();


        // 1. Calculate total of amount_mu for records up to the current date
        $current_month_total = LoanPaymentDue::where('loan_id', $loanId)
            ->where('payment_status', 0)
            ->where('due_date', '<=', $early_settle_date)
            ->sum('amount_mu');

        // 2. Calculate average daily amount_mu for the next month
        $next_month_avg_daily = LoanPaymentDue::where('loan_id', $loanId)
            ->where('payment_status', 0)
            ->where('due_date', '>', $early_settle_date)
            ->where('due_date', '<=', date('Y-m-d', strtotime('+1 month', strtotime($early_settle_date))))
            ->sum('amount_mu') / 30;


        // 3. Multiply by the number of days that have passed in the current month
        if ($loan_recovered) {
            //            $recoveredDate = Carbon::parse($loan_recovered->recovered_date);
            //            $last_payment_due = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId])
            //                ->whereYear('due_date', $recoveredDate->year)
            //                ->whereMonth('due_date', $recoveredDate->month)
            //                ->orderBy('id', 'desc')
            //                ->first();
            //            dd($last_payment_due->due_date, $early_settle_date);
            $days_passed = date('j', strtotime('-1 day', strtotime($early_settle_date)));  // Get day of the month without leading zeros
            //            $days_diff1 = ((strtotime(date('Y-m-d', strtotime($early_settle_date))) - strtotime($last_payment_due->due_date)) / (60 * 60 * 24));
            //            $days_passed = abs($days_diff1);
        } else {
            $days_diff1 = ((strtotime(date('Y-m-d')) - strtotime($OutstandingData ? $OutstandingData->due_date : $LoansInfo->disb_date)) / (60 * 60 * 24));
            $days_passed = abs($days_diff1);
        }
        $adjusted_next_month_total = $next_month_avg_daily * $days_passed;

        $total_amount_profit1 = round($current_month_total + $adjusted_next_month_total);
        $total_amount_profit = round($adjusted_next_month_total);


        if (!$OutstandingData) {
            $OutstandingData = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 0])->first();
            $outstanding = $LoansInfo->total_amount_pr;
        } else {
            $outstanding = $OutstandingData->outstanding;
        }

        $loan_payment_dues = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'loan_payment_due.due_status' => 0])
            ->select('loan_payment_due.*')
            ->orderBy("loan_payment_due.due_date")
            ->get()->toArray();
        $charity_amount = 0;
        foreach ($loan_payment_dues as $record) {
            $charity_amount += (int)$record['charity_amount'];
        }

        if ($OutstandingData) {
            $due_date = $OutstandingData->due_date;
            $installment_no = $OutstandingData->installment_no;
            //$outstanding = $OutstandingData->outstanding;
            if ($installment_no < 12 && $OutstandingData->payment_status == 1) {
                $charges = 4.5;
            } else if ($installment_no == 12 || $installment_no <= 23 && $OutstandingData->payment_status == 1) {
                $charges = 3;
            } else if ($installment_no == 24 || $installment_no <= 35 && $OutstandingData->payment_status == 1) {
                $charges = 1.5;
            } else {
                $charges = 0;
            }

            $now = strtotime($early_settle_date); //time(); // or your date as well
            $your_date = strtotime($due_date);
            $datediff = $now - $your_date;
            if ($datediff < 0) {
                $datediff = $datediff * -1;
            }


            $givenDate = new DateTime($due_date);  // Replace with your given date
            $currentDate = new DateTime($early_settle_date);  // Current date

            // Calculate difference in months and days
            $diffMonths = ($currentDate->format('Y') - $givenDate->format('Y')) * 12 + $currentDate->format('m') - $givenDate->format('m');
            $diffDays = $currentDate->format('d') - $givenDate->format('d');

            // Calculate total difference in days considering each month as 30 days
            $totalDiffDays = ($diffMonths * 30) + $diffDays;


            //            $Profit = $total_amount_profit;
            $Profit = !$loan_recovered ? round($next_month_avg_daily) * $totalDiffDays : $total_amount_profit1;

            $SettlementCharges = $outstanding * ($charges / 100);

            $FED = $SettlementCharges * (13 / 100);

            $TotalSettlement = $outstanding + $Profit + $SettlementCharges + $FED + $charity_amount;
            $TotalSettlement = round($TotalSettlement);

            $TotalCharity = $charity_amount;
            $d['loanId'] = $loanId;
            $d['outstanding'] = $outstanding;
            //            $d['days_diff'] = $days_diff;
            $d['days_diff'] = $totalDiffDays;
            $d['Profit'] = round($Profit);
            $d['SettlementCharges'] = round($SettlementCharges);
            $d['FED'] = round($FED);
            $d['TotalSettlement'] = round($TotalSettlement);
            $d['TotalCharity'] = round($TotalCharity);
            $d['OutstandingData'] = $OutstandingData;
            $d['loan_recovered'] = $loan_recovered;
            return view('loan-payment-recovered.earlysettle', $d);
        }
        /*
          1st Year	4.50%
          2nd Year	3%
          3rd Year	1.50%
          4th Year onwards	0%
          Pay off date	01-Mar-23

         */
        /*
          Settlement Amount
          Outstanding Principal	 9,957,847
          Profit for 14 days (Feb 16 - Marc 1)	 65,832
          Settlement Charges	 448,103 	(because of 1st year)
          FED on Settlement Charges	 58,253
          Total Settlement Amount	 10,530,036

         */
    }

    public function index()
    {
        $loanPaymentRecovereds = LoanPaymentRecovered::get();

        return view('loan-payment-recovered.index', compact('loanPaymentRecovereds'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $loanPaymentRecovered = new LoanPaymentRecovered();
        return view('loan-payment-recovered.create', compact('loanPaymentRecovered'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(LoanPaymentRecovered::$rules);

        $loanPaymentRecovered = LoanPaymentRecovered::create($request->all());

        return redirect()->route('loan-payment-recovereds.index')
            ->with('flash_success', 'Loan Payment created successfully.');
    }

    public function earlypay(Request $request)
    {
        $loanId = $request->loanId;
        $remaining_installment = LoanPaymentDue::where('loan_id', $loanId)
            ->where('due_date', '>=', date('Y-m-01'))
            ->update([
                'is_settle' => 1
            ]);
        //        $dueInfo = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 0])->orderBy('id')->limit(1)->first();
        //        if ($dueInfo) {
        //            $DueId = $dueInfo->id;
        //            \App\Models\LoanPaymentDue::where('id', $DueId)->update(['payment_status' => 1]);
        //        }


        $amount_outstanding = $request->amount_outstanding;
        $amount_total = $request->amount_total;
        $amount_profit = $request->amount_profit;
        $amount_fed = $request->amount_fed;
        $amount_settlement = $request->amount_settlement;
        $amount_total = $request->amount_total;
        $recovered_date = date("Y-m-d");
        $bank_slip_id = 1;
        $userId = \Illuminate\Support\Facades\Auth::user()->id;
        $PayData = [
            'loan_id' => $loanId,
            'due_id' => 0,
            'amount_total' => $amount_total,
            'amount_pr' => $amount_outstanding,
            'amount_mu' => $amount_profit,
            'amount_fed' => $amount_fed,
            'amount_settlement' => $amount_settlement,
            'amount_takaful' => 0,
            'amount_charity' => $request->amount_charity,
            'recovered_by' => $userId,
            'recovered_date' => $recovered_date,
            'bank_slip_id' => $bank_slip_id,
            'is_settle' => 1,
            'locked_profit' => $request->profit
        ];


        LoanPaymentRecovered::create($PayData);
        \App\Models\LoanHistory::where('id', $loanId)->update(['loan_status_id' => 7]);
        return redirect()->route('loan-payment-recovereds.index')
            ->with('flash_success', 'Loan Payment created successfully.');
    }

    public function runKiborRevision($loanId)
    {


        // echo $loanId;

        $KiborNew = \App\Models\LoanKiborRate::where('status', 1)->first();
        $Kibor_RateNew = $KiborNew->kibor_rate;
        $Spread_RateNew = $KiborNew->spread_rate;

        if (!(\App\Models\LoanPaymentDue::where('loan_id', $loanId)->first())) {
            return back()->with('flash_error', 'Schedule does not exist');
        }


        $A_outstanding = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();
        $loan_history = \App\Models\LoanHistory::where('id', $loanId)->first();

        if ($A_outstanding) {
            $outstanding = $A_outstanding->outstanding;
        } else {
            $A_outstanding = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId])->first();
            $outstanding = $loan_history->total_amount_pr;
        }

        $due_date = $A_outstanding->due_date;

        //echo "Outstanding: ".$outstanding."<br>";
        $loan_period = $loan_history->loan_period - $A_outstanding->installment_no;
        \App\Models\LoanHistory::find($loanId)->update([
            'kibor_rate' => $Kibor_RateNew,
            'spread_rate' => $Spread_RateNew,
            'kibor_revision_date' => date("Y-m-d")
        ]);

        print $this->GenerateRepaymentScheduleDecline($loanId, $outstanding, $due_date, $loan_period, $loan_history, true);
        dd($KiborNew);
    }

    public function pay_partial($loanId)
    {
        $d['loanId'] = $loanId;
        $OD_Check = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'due_date' => "<" . date("Y-m-d")])->get();
        $d['overdue'] = $OD_Check;
        return view('loan-payment-recovered.partial', $d);
    }

    public function showpartial(Request $request)
    {
        $data = $request->all();

        $loanId = $data['loanId'];
        $percent = $data['percent'];

        if (!(\App\Models\LoanPaymentDue::where('loan_id', $loanId)->first())) {
            return back()->with('flash_error', 'Schedule does not exist');
        }

        $partial_date = date("Y-m-d", strtotime($data['date']));

        $due_data = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'due_date' => $partial_date])->first();

        // if ($due_data && $due_data->payment_status == 0) {
        //     return back()->with('flash_error', 'Due payment is unpaid, so partial can not be run');
        //     //die("Due payment is unpaid, so partial can not be run");
        // }
        if ($partial_date > date("Y-m-d")) {
            // if ($partial_date > "2023-02-20") {
            return back()->with('flash_error', 'Partial Date is in future, so partial can not be run');
            //die("Partial Date is in future, so partial can not be run");
        }

        $outstanding = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();
        $loan_history = \App\Models\LoanHistory::where('id', $loanId)->first();

        if ($outstanding) {
            $outstanding = $outstanding->outstanding;
        } else {
            $outstanding = $loan_history->total_amount_pr;
        }

        $partial = ($outstanding) * $percent / 100;

        $markup_percent = 0;
        if ($percent > 25)
            $markup_percent = $percent - 25;
        $upper_partial = 0;
        if ($markup_percent > 0) {
            $upper_partial = ($outstanding) * $markup_percent / 100;
        }

        if ($due_data->installment_no <= 12) {
            $charges = 4.5;
        } else if ($due_data->installment_no <= 24) {
            $charges = 3;
        } else if ($due_data->installment_no <= 36) {
            $charges = 1.5;
        } else {
            $charges = 0;
        }

        $markup = $upper_partial * ($charges / 100);

        $d['loanId'] = $data['loanId'];
        $d['percent'] = $data['percent'];
        $d['showpartial'] = $data['date'];
        $d['outstanding'] = $outstanding;
        $d['partial'] = $partial;
        $d['upper_partial'] = $upper_partial;
        $d['markup_percent'] = $markup_percent;
        $d['markup'] = $markup;

        //        $TotalRemainingOutstanding = $outstanding - ($partial);
        //        $loan_period = $loan_history->loan_period - $due_data->installment_no;
        //
        //        echo "<pre>";
        //        print_r($d);
        //        echo "</pre>";
        return view('loan-payment-recovered.partialshow', $d);
    }

    public function store_partial(Request $request)
    {
        $data = $request->all();
        $loanId = $data['loanId'];
        $percent = $data['percent'];

        // dd('d');

        if (!(\App\Models\LoanPaymentDue::where('loan_id', $loanId)->first())) {
            return back()->with('flash_error', 'Schedule does not exist');
        }

        $partial_date = date("Y-m-d", strtotime($data['date']));
        $due_data = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'due_date' => $partial_date])->first();

        // if ($due_data && $due_data->payment_status == 0) {
        //     return back()->with('flash_error', 'Due payment is unpaid, so partial can not be run');
        //     die("Due payment is unpaid, so partial can not be run");
        // }
        if ($partial_date > date("Y-m-d")) {
            //        if ($partial_date > "2023-02-20") {
            return back()->with('flash_error', 'Partial Date is in future, so partial can not be run');
            die("Partial Date is in future, so partial can not be run");
        }
        $outstanding = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();
        $loan_history = \App\Models\LoanHistory::where('id', $loanId)->first();

        if ($outstanding) {
            $outstanding = $outstanding->outstanding;
        } else {
            $outstanding = $loan_history->total_amount_pr;
        }

        $partial = ($outstanding) * $percent / 100;

        $markup_percent = 0;
        if ($percent > 25)
            $markup_percent = $percent - 25;
        $upper_partial = 0;
        if ($markup_percent > 0) {
            $upper_partial = ($outstanding) * $markup_percent / 100;
        }

        if ($due_data->installment_no <= 12) {
            $charges = 4.5;
        } else if ($due_data->installment_no <= 24) {
            $charges = 3;
        } else if ($due_data->installment_no <= 36) {
            $charges = 1.5;
        } else {
            $charges = 0;
        }

        $markup = $upper_partial * ($charges / 100);

        $d['loanId'] = $data['loanId'];
        $d['percent'] = $data['percent'];
        $d['outstanding'] = $outstanding;
        $d['partial'] = $partial;
        $d['upper_partial'] = $upper_partial;
        $d['markup_percent'] = $markup_percent;
        $d['markup'] = $markup;

        $TotalRemainingOutstanding = $outstanding - ($partial);
        $loan_period = $loan_history->loan_period - $due_data->installment_no;

        //        echo "<pre>";
        //        print_r($d);
        //        echo "</pre>";
        //
        //        die;
        //        $AboveData = \App\Models\LoanPaymentDue::where("due_date", $partial_date)->get()->toArray();
        $AboveData = \App\Models\LoanPaymentDue::where([
            ["due_date", ">", $partial_date],
            ['loan_id', '=', $loanId]
        ])->update(['due_status' => 1]);

        //        dd($AboveData);
        //        
        $amount_total = $partial + $markup;
        $amount_pr = $partial;
        $recovered_date = date("Y-m-d");
        $bank_slip_id = 1;
        $userId = \Illuminate\Support\Facades\Auth::user()->id;

        $PayData = [
            'loan_id' => $loanId,
            'due_id' => $due_data->id,
            'amount_total' => $amount_total,
            'amount_pr' => $amount_pr,
            'amount_mu' => $markup,
            'amount_fed' => 0,
            'amount_settlement' => 0,
            'amount_takaful' => 0,
            'recovered_by' => $userId,
            'payment_type' => 2,
            'recovered_date' => $partial_date,
            'bank_slip_id' => $bank_slip_id
        ];
        //        dd($PayData);
        LoanPaymentRecovered::create($PayData);

        $partial_status = $this->GenerateRepaymentScheduleDecline($loanId, $TotalRemainingOutstanding, $partial_date, $loan_period, $loan_history);

        $tt = new \App\Repositories\LoanHistoryRepo;
        $d['tt_records'] = $tt->getAll();
        //        dd($d['tt_records']);
        //        return view('lms_loans.loandetails', $d);
        // dd('fff');

        if ($partial_status) { 
            //            return view('lms_loans.loandetails', $d)->with('flash_success', 'Partial processed successfully');
            return redirect()->route('loans.partial', $loanId)->with('flash_error', 'Partial processed successfully');
        } else {
            // dd('else');

            //            return view('lms_loans.loandetails', $d)->with('flash_error', 'Due payment is unpaid, so partial can not be run');
            return redirect()->route('loans.partial', $loanId)->with('flash_error', 'Due payment is unpaid, so partial can not be run');
        }
        //dd($d);
        //return view('loan-payment-recovered.partial', $d);
    }

    function GenerateRepaymentScheduleDecline($LoanId, $oustanding, $disb_date, $loan_period, $data, $debug = false)
    {       
            //    print_r($LoanId);
            //    print_r($oustanding);
            //    print_r($disb_date);
            //    print_r($loan_period);
            //    print_r($data); 
            //    print_r($debug); 
            //    die;

        $takaful_amount = 0;
        $fed_amount = 0;
        $kibor_rate = 0;
        $spread_rate = 0;

        //dd($data);
        $data = \App\Models\LoanHistory::where('id', $LoanId)->first();
        // echo $disb_date ;die;

        $disb_day = date("d", strtotime($disb_date));
        // echo $disb_day ;die;

        if ($disb_day >= 2 && $disb_day <= 5) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-05";
        }
        if ($disb_day >= 6 && $disb_day <= 10) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-10";
        }
        if ($disb_day >= 11 && $disb_day <= 15) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-15";
        }
        if ($disb_day >= 16 && $disb_day <= 20) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-20";
        }
        if ($disb_day >= 21 && $disb_day <= 25) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-25";
        }
        if ($disb_day >= 26 && $disb_day <= 1) {
            // dd($disb_day);
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-01";
        }
            //    echo $disb_date."<br>" ;
            //    echo $rep_start_date; die;
        $loan_freq = $data['loan_frequency'];
        $markup_rate = $data['markup_rate'];
        //        $loan_period = $data['loan_period'];

        $ChequeDate = $disb_date;
        $ApprovedLoanAmount = round($oustanding); //$amount_pr;
        //        $SrChargeRate = $markup_rate;
        $SrChargeRate = $data['kibor_rate'] + $data['spread_rate'];
        $LoanFrequency = $loan_freq;
        $LoanTerm = $loan_period;
        $RepStartDate = $rep_start_date;

        $DueData = 0; //\App\Models\LoanPaymentDue::where(array('loan_id' => $LoanId))->exists();

        if ($DueData) {
            return 0;
        }
        $DayRepStart = date("d", strtotime($RepStartDate));
        //if ($DayRepStart < 15 || $DayRepStart > 25) {
        //  $DayRepStart = 15;
        $RepStartDate = date("Y-m", strtotime($RepStartDate)) . "-" . $DayRepStart;
        //}

        $GrandPrinc = $GrandServ = $GrandTotal = $GrandDays = $GrandTakaful = 0;

        $iLoanFrequency = $LoanFrequency;
        $Return = "<table width='100%' border='1' bordercolor='#999999' cellspacing='0' cellpadding='4'>";
        $Return .= "<tr bgcolor='#CCCCCC'>"
            . "<td  align='center' rowspan='2'>Sr#</td>"
            . "<td align='center' rowspan='2'>Schedule Date</td>"
            . "<td align='center' rowspan='2'>Days</td>"
            . "<td  colspan='3' align='center'>Dues</td><td rowspan='2'  align='center'>Balance</td></tr>"
            . "<tr bgcolor='#CCCCCC'><td align='center'>Principle</td><td align='center'>Srv Charge</td><td align='center'>Takaful</td><td align='center'>Total</td></tr>";

        $Return .= "<tr><td colspan='6' align='right'></td><td align='right'>$ApprovedLoanAmount</td></tr>";

        $arryModeOfPayment = array(
            1 => 1,
            3 => 3,
            7 => 4,
            6 => 6,
            4 => $LoanTerm,
            8 => 12
        );
        $LoanFrequency = $arryModeOfPayment[$iLoanFrequency];
        $arryModeOfPaymentCalc = array(
            1 => 12,
            3 => 4,
            7 => 3,
            6 => 2,
            4 => 1,
            8 => 1
        );

        $SchedLoanTerm = $LoanTerm;
        $FormulaloanTerm = $LoanTerm / 12;
        $modeOfP = $arryModeOfPaymentCalc[$iLoanFrequency];

        $fv = 0;
        $pv = $ApprovedLoanAmount;
        $rate = ($SrChargeRate / 100) / 360 * 30;
        $nper = $LoanTerm;
        $type = 0;

        $PMT = ((0 - $pv * pow(1 + $rate, $nper)) /
            (1 + $rate) /
            ((pow(1 + $rate, $nper) - 1) / $rate)) * -1;

        $rate = 16;
        $rate = $kibor_rate + $spread_rate;
        //echo $this->calPMT($rate, 7, $ApprovedLoanAmount);
        //dd();        
        //dd(round($PMT,6));
        //dd($PMT);
        $Fst = ($SrChargeRate / $modeOfP) / 100;
        $Snd = pow((1 + $Fst), ($modeOfP * $FormulaloanTerm));
        $Trd = 1 / $Snd;
        $Fth = 1 - $Trd;
        //$Ffth = round($Fth / $Fst, 2);
        //$Final = round($ApprovedLoanAmount / $Ffth);
        $Ffth = $Fth / $Fst;
        //$Final = round($ApprovedLoanAmount / $Ffth);
        $Final = $ApprovedLoanAmount / $Ffth;

        //echo "Final1: " . $Final . "<br>";
        $TotalSrCharge = $ApprovedLoanAmount * ($SrChargeRate / 100);
        //echo "Final2: " . $TotalSrCharge . "<br>";
        $DailySrcCharge = $TotalSrCharge / 365;
        $TotalDaysLoanTerms = $SchedLoanTerm * (365 / 12);

        //echo "TotalDaysLoanTerms: " . $TotalDaysLoanTerms . "<br>";
        //echo "DailySrcCharge: " . $DailySrcCharge . "<br>";
        //Loop Here
        for ($i = 1; $i <= ($SchedLoanTerm / $LoanFrequency); $i++) {

            if ($i == 1) {
                $Dev_ScheduleDate = $RepStartDate;
            } else {
                $date = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate)));
                $date->modify('+' . $LoanFrequency . ' month');
                $Dev_ScheduleDate = $date->format('Y-m-d');
            }
            $MonthlyServiceCharge = $ApprovedLoanAmount * $Fst;
            $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
            $ApprovedLoanAmount -= $MonthlyPrinciple;
            $MonthlyTakaful = 0;

            if ($i == ($SchedLoanTerm / $LoanFrequency)) {
                if ($ApprovedLoanAmount <> 0) {
                    $MonthlyPrinciple += $ApprovedLoanAmount;
                    $ApprovedLoanAmount = 0;
                }
            }

            $Total = $MonthlyPrinciple + $MonthlyServiceCharge + $MonthlyTakaful;

            $sScheduledRepaymentDate = date("M j, Y", strtotime($Dev_ScheduleDate));
            $sScheduledDay = date('D', strtotime($sScheduledRepaymentDate));
            $sScheduledDate = date('d', strtotime($sScheduledRepaymentDate));
            if ($sScheduledDay == "Sun") {
                $dateScheduledRepaymentDate = new DateTime(date("Y-m-d", strtotime($sScheduledRepaymentDate)));
                if ($sScheduledDate == 25) {
                    //$dateScheduledRepaymentDate->modify('-1 day');
                } else {
                    //$dateScheduledRepaymentDate->modify('+1 day');
                }
                $sScheduledRepaymentDate = $dateScheduledRepaymentDate->format('Y-m-d');
                $sScheduledRepaymentDate = date("M j, Y", strtotime($sScheduledRepaymentDate));
            }
            $MysqlScheduleDate = date("Y-m-d", strtotime($sScheduledRepaymentDate));

            $DueArray = [
                'loan_id' => $LoanId,
                'installment_no' => $i,
                'due_date' => $MysqlScheduleDate,
                'amount_total' => $Total,
                'amount_pr' => $MonthlyPrinciple,
                'outstanding' => $ApprovedLoanAmount,
                'amount_mu' => $MonthlyServiceCharge,
                'amount_takaful' => $MonthlyTakaful
            ];

            if (!$debug)
                \App\Models\LoanPaymentDue::create($DueArray);

            $Return .= "<tr>"
                . "<td>$i</td>"
                . "<td>$MysqlScheduleDate</td>"
                . "<td align='right'>$MonthlyPrinciple</td>"
                . "<td align='right'>$MonthlyServiceCharge</td>"
                . "<td align='right'>$MonthlyTakaful</td>"
                . "<td align='right'>" . ($Total) . "</td>"
                . "<td align='right'>" . ($ApprovedLoanAmount) . "</td>"
                . "</tr>";

            $GrandPrinc += $MonthlyPrinciple;
            $GrandServ += $MonthlyServiceCharge;
            $GrandTotal += $Total;
            $GrandTakaful += $MonthlyTakaful;
        }
        //        \App\Models\LoanHistory::find($LoanId)->update(
        //                [
        //                    'loan_status_id' => 10,
        //                    'kibor_rate' => $kibor_rate,
        //                    'spread_rate' => $spread_rate,
        //                    'takaful' => $takaful_amount,
        //                    'total_amount' => $GrandTotal,
        //                    'total_amount_pr' => $GrandPrinc,
        //                    'total_amount_mu' => $GrandServ
        //        ]);

        $LastSeries = \App\Models\FinGeneralLedger::orderBy("id", "desc")->first();
        if (!isset($LastSeries->txn_series)) {
            $LastSeries = 0;
        } else {
            $LastSeries = $LastSeries->txn_series;
        }
        $NextSeries = $LastSeries + 1;

        $ProcessingFees = $GrandPrinc * 1.5 / 100;
        if ($fed_amount) {
            $FED = $ProcessingFees * 13 / 100;
        } else {
            $FED = 0;
        }
        $TakafulFees = $GrandPrinc * 0.8 / 100;
        $BankPayment = $GrandPrinc - ($ProcessingFees + $TakafulFees);

        /*
          //        Loan Processing fee income and FED payable
          //First Entry ()


          $Model_GL = \App\Models\FinGeneralLedger::create([
          'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'Disbursement Voucher - Loan Processing fee income and FED payable', 'debit' => $GrandPrinc, 'credit' => $GrandPrinc, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
          ]);
          $FinGL_Id = $Model_GL->id;
          //JS Bank
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => ($ProcessingFees + $FED), 'credit' => 0]);

          //Processing Fees
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '336', 'debit' => 0, 'credit' => $ProcessingFees]);
          if ($FED) {
          //FED Fees
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '216', 'debit' => 0, 'credit' => $FED]);

          $Model_GL = \App\Models\FinGeneralLedger::create([
          'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'payment of FED on loan processing fee', 'debit' => $FED, 'credit' => $FED, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
          ]);
          $FinGL_Id = $Model_GL->id;

          //JS Bank
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => 0, 'credit' => $FED]);
          //FED Fees
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '216', 'debit' => $FED, 'credit' => 0]);
          }
          //First Entry ()
          $Model_GL = \App\Models\FinGeneralLedger::create([
          'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'Disbursement Voucher', 'debit' => $GrandPrinc, 'credit' => $GrandPrinc, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
          ]);
          $FinGL_Id = $Model_GL->id;
          //Lendings To Financial Institutions
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '147', 'debit' => $GrandPrinc, 'credit' => '0']);
          //JS Bank
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => 0, 'credit' => $GrandPrinc]);

          //Second Entry (Takaful)
          if ($TakafulFees) {
          //Takaful first
          $Model_GL = \App\Models\FinGeneralLedger::create([
          'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'Takaful Voucher - Payment Received From Borrower', 'debit' => $GrandPrinc, 'credit' => $GrandPrinc, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
          ]);
          $FinGL_Id = $Model_GL->id;
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '187', 'debit' => 0, 'credit' => $TakafulFees]);
          //JS Bank
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => $TakafulFees, 'credit' => 0]);

          //Takaful second
          $Model_GL = \App\Models\FinGeneralLedger::create([
          'slip_id' => 1, 'loan_id' => $LoanId, 'user_id' => 1, 'details' => 'Takaful Voucher - Payment to Takaful Company', 'debit' => $GrandPrinc, 'credit' => $GrandPrinc, 'txn_date' => date('Y-m-d'), 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
          ]);
          $FinGL_Id = $Model_GL->id;
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '187', 'debit' => $TakafulFees, 'credit' => 0]);
          //JS Bank
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => 0, 'credit' => $TakafulFees]);
          }
          //Processing
          \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '337', 'debit' => 0, 'credit' => $ProcessingFees]);

         */
        $dLoanBalance = $GrandServ + $GrandPrinc;

        $Return .= "<tr>"
            . "<td colspan='3'>Total</td>"
            . "<td align='right'>$GrandPrinc</td>"
            . "<td align='right'>$GrandServ</td>"
            . "<td align='right'>$GrandTakaful</td>"
            . "<td align='right'>$GrandTotal</td>"
            . "<td align='right'>$ApprovedLoanAmount</td>"
            . "</tr>";
        $Return .= "</table>";
        return $Return;
    }

    function calPMT($apr, $term, $loan)
    {
        $term = $term * 12;
        $apr = $apr / 1200;
        $amount = $apr * -$loan * pow((1 + $apr), $term) / (1 - pow((1 + $apr), $term));
        return round($amount);
    }

    function PostBulkPayment(Request $request)
    {
        $data = $request->all();
        $payset = $data['payset'];
        $recovered_by = \Illuminate\Support\Facades\Auth::user()->id;
        $recovered_date = date("Y-m-d H:i:s");
        $bank_slip_id = 1;
        if ($payset) {
            foreach ($payset as $row) {
                $row = json_decode($row, true);
                $due_date = $row['due_date'];

                $loan_id = $row['loan_id'];
                $installment_no = $row['installment_no']--;
                // $loan_payment_id = $row['loan_payment_id'];
                $dateString = $due_date; // Replace with your desired date
                $timestamp = strtotime($dateString);

                $previousMonthTimestamp = strtotime('-1 month', $timestamp);
                $previousMonthDate = date('m', $previousMonthTimestamp);

                if ($installment_no > 1) {
                    $loan_payment_dues = \App\Models\LoanPaymentDue::where('loan_id', $loan_id)->whereMonth('due_date', $previousMonthDate)->first();
                    if ($loan_payment_dues->payment_status == 0) {
                        return redirect()->back()->with('message', 'Please pay the previous installment first.');
                    }
                }

                $DueData = \App\Models\LoanPaymentDue::where('loan_id', $loan_id)->where('due_date', $due_date)->where('due_status', 0)->first();
                LoanPaymentRecovered::create([
                    'loan_id' => $loan_id,
                    'due_id' => $DueData->id,
                    'amount_total' => (int)$DueData->amount_total,
                    'amount_pr' => (int)$DueData->amount_pr,
                    'amount_mu' => (int)$DueData->amount_mu,
                    'amount_takaful' => (int)$DueData->amount_takaful,
                    'recovered_by' => $recovered_by,
                    'recovered_date' => $recovered_date,
                    'bank_slip_id' => $bank_slip_id
                ]);
                \App\Models\LoanPaymentDue::where('id', $DueData->id)->update(['payment_status' => 1, 'payment_at' => $recovered_date]);

                //Sending SMS
                $text = 'Dear Customer, Thank you for your payment of Rs. ' . number_format((int)$DueData->amount_total) . ' against housing finance facility. Your monthly installment for the month of ' . date('F', strtotime($recovered_date)) . ' ' . date('Y', strtotime($recovered_date)) . ' has been cleared.';
                $this->sendSms($text);
            }
            return back()->with('flash_success', 'Installments marked paid successfully');
        } else {
            return back()->with('flash_error', 'Nothing to process');
        }
    }

    public function storepay(Request $request)
    {

        request()->validate(LoanPaymentRecovered::$rules);
        $minduedate = $request->minduedate;
        $recovered_date = $request->recovered_date;
        $recovered_time = strtotime($recovered_date);
        $minduedate_time = strtotime($minduedate);

        if ($recovered_time < $minduedate_time) {
            return back()->with('flash_error', 'Due dute must not be after payment date');
        }
        //dd("hello");
        //echo auth()->user()->name;
        //dd(auth()->user());
        $loanId = $request->loan_id;
        $payableAmount = $request->amount_total;
        $recovered_by = \Illuminate\Support\Facades\Auth::user()->id; //$request->recovered_by;
        $bank_slip_id = 0; //$request->bank_slip_id;
        //dd($request->all());

        $TotalAmountDue = \App\Models\LoanPaymentDue::where(
            ['loan_id' => $loanId, 'payment_status' => 0, ['due_date', '<=', date("Y-m-d")]]
        )->sum('amount_total');
        $DueData = \App\Models\LoanPaymentDue::where(
            ['loan_id' => $loanId, 'payment_status' => 0, 'due_status' => 0]
        )->get();
        $balancePayableAmount = $payableAmount;
        $PaidArray = [];
        $PartialPaidArray = [];
        //echo $balancePayableAmount;
        //echo "<br>";
        foreach ($DueData as $Dues) {
            $DueId = $Dues->id;
            $DueAmountTotal = (int) $Dues->amount_total;
            $DuePr = (int) $Dues->amount_pr;
            $DueMu = (int) $Dues->amount_mu;
            $DueTk = $Dues->amount_takaful;

            $PaidRecord = LoanPaymentRecovered::where(['due_id' => $DueId])->first();
            if ($PaidRecord) {
                $DueAmountTotal -= $PaidRecord->amount_total;
                $DuePr -= $PaidRecord->amount_pr;
                $DueMu -= $PaidRecord->amount_mu;
                $DueTk -= $PaidRecord->amount_takaful;
            }
            //dd($PaidRecord);
            if ($balancePayableAmount > 0 && $balancePayableAmount >= $DueAmountTotal) {
                $balancePayableAmount -= $DueAmountTotal;
                $PaidArray[$DueId] = [$DueAmountTotal, $DuePr, $DueMu, $DueTk, 1];
            } else if ($balancePayableAmount > 0 && $balancePayableAmount < $DueAmountTotal) {

                if ($balancePayableAmount >= $DueMu) {
                    if ($balancePayableAmount == $DueMu) {
                        $DueMu = $balancePayableAmount;
                        $DuePr = 0;
                        $DueTk = 0;
                    } else {


                        $balancePayableAmount -= $DueMu;
                        if ($balancePayableAmount >= $DueTk) {
                            $balancePayableAmount -= $DueTk;
                        }
                        if ($balancePayableAmount > 0) {
                            $DuePr = $balancePayableAmount;
                        } else {
                            $DuePr = 0;
                        }
                    }
                } else {
                    $DueMu = $balancePayableAmount;
                    $DuePr = 0;
                    $DueTk = 0;
                }
                $DueAmountTotal = $DuePr + $DueMu + $DueTk;
                $balancePayableAmount -= $DueAmountTotal;
                $PaidArray[$DueId] = [$DueAmountTotal, $DuePr, $DueMu, $DueTk, 0];
            } else {
                break;
            }
        }
        //echo $balancePayableAmount;
        //echo "<br>";
        //dd($PaidArray);



        DB::beginTransaction();

        try {
            $PaidPrincipal = $PaidMarkup = $PaidTakaful = 0;
            foreach ($PaidArray as $DueId => $DueData) {
                $PayData = [
                    'loan_id' => $loanId,
                    'due_id' => $DueId,
                    'amount_total' => $DueData[0],
                    'amount_pr' => $DueData[1],
                    'amount_mu' => $DueData[2],
                    'amount_takaful' => $DueData[3],
                    'recovered_by' => $recovered_by,
                    'recovered_date' => $recovered_date,
                    'bank_slip_id' => $bank_slip_id
                ];
                LoanPaymentRecovered::create($PayData);

                if ($DueData[4] == 1) {
                    $DueDataUpdate = [
                        'payment_status' => $DueData[4],
                        'payment_at' => $recovered_date
                    ];
                    \App\Models\LoanPaymentDue::find($DueId)->update($DueDataUpdate);
                }

                $PaidPrincipal += $DueData[1];
                $PaidMarkup += $DueData[2];
                $PaidTakaful += $DueData[3];
            }


            // $LastSeries = \App\Models\FinGeneralLedger::orderBy("id", "desc")->first()->txn_series;
            //$NextSeries = $LastSeries + 1;
            //Maintain General Ledger

            /*
              $Model_GL = \App\Models\FinGeneralLedger::create([
              'slip_id' => 1, 'loan_id' => $loanId, 'user_id' => 1, 'details' => 'Repayment Voucher', 'debit' => $payableAmount, 'credit' => $payableAmount, 'txn_date' => $recovered_date, 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
              ]);
              $FinGL_Id = $Model_GL->id;
              //JS Bank
              \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '172', 'debit' => $payableAmount, 'credit' => 0]);
              //Lendings To Financial Institutions
              \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '147', 'debit' => 0, 'credit' => $PaidPrincipal]);
              //Takaful
              if ($PaidTakaful) {
              \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '187', 'debit' => 0, 'credit' => $PaidTakaful]);
              }

              //Profit/Markup
              \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '177', 'debit' => 0, 'credit' => $PaidMarkup]);

              //Maintain General Ledger
              $Model_GL = \App\Models\FinGeneralLedger::create([
              'slip_id' => 1, 'user_id' => 1, 'details' => 'Profit Voucher', 'debit' => $PaidMarkup, 'credit' => $PaidMarkup, 'txn_date' => $recovered_date, 'txn_type' => 1, 'txn_series' => $NextSeries++, 'office_id' => 1
              ]);
              $FinGL_Id = $Model_GL->id;
              //Profit/Markup Rec
              \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '177', 'debit' => $PaidMarkup, 'credit' => 0]);
              //Profit on lending
              \App\Models\FinGeneralLedgerDetail::create(['fin_gen_id' => $FinGL_Id, 'coa_id' => '331', 'debit' => 0, 'credit' => $PaidMarkup]);
             */
            DB::commit();

            //Sending SMS
            $user = LoanHistory::find($request->loan_id);
            $text = 'Dear Customer, Thank you for your payment of Rs. ' . number_format($request->amount_total) . ' against housing finance facility. Your monthly installment for the month of ' . date('F', strtotime($request->recovered_date)) . ' ' . date('Y', strtotime($request->recovered_date)) . ' has been cleared.';
            $this->sendSms($text);

            return redirect()->route('ttr.show_schedule', $loanId)
                ->with('flash_success', 'Loan Payment created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ttr.show_schedule', $loanId)
                ->with('flash_error', 'Loan Payment Failed.');
        }


        //echo $balancePayableAmount;
        //echo "<br>";
        //dd($PaidArray);
        //$loanPaymentRecovered = LoanPaymentRecovered::create($request->all());

        return redirect()->route('ttr.show_schedule', $loanId)
            ->with('flash_success', 'Loan Payment created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loanPaymentRecovered = LoanPaymentRecovered::find($id);

        return view('loan-payment-recovered.show', compact('loanPaymentRecovered'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $loanPaymentRecovered = LoanPaymentRecovered::find($id);

        return view('loan-payment-recovered.edit', compact('loanPaymentRecovered'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  LoanPaymentRecovered $loanPaymentRecovered
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoanPaymentRecovered $loanPaymentRecovered)
    {
        request()->validate(LoanPaymentRecovered::$rules);

        $loanPaymentRecovered->update($request->all());

        return redirect()->route('loan-payment-recovereds.index')
            ->with('flash_success', 'Loan Payment updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $loanPaymentRecovered = LoanPaymentRecovered::find($id)->delete();

        return redirect()->route('loan-payment-recovereds.index')
            ->with('flash_success', 'Loan Payment deleted successfully');
    }

    public function getRepStartDate($disb_date)
    {
        $disb_day = date("d", strtotime($disb_date));
        if ($disb_day >= 2 && $disb_day <= 5) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-05";
        }
        if ($disb_day >= 6 && $disb_day <= 10) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-10";
        }
        if ($disb_day >= 11 && $disb_day <= 15) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-15";
        }
        if ($disb_day >= 16 && $disb_day <= 20) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-20";
        }
        if ($disb_day >= 21 && $disb_day <= 25) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-25";
        }
        if ($disb_day >= 26 || $disb_day <= 1) {
            $rep_start_date = date("Y-m", strtotime($disb_date . "+1 month")) . "-01";
        }
        //        echo $disb_date;
        //        echo "<br>";
        //        echo $disb_day;
        //        echo "<br>";
        //        echo $rep_start_date;
        //        die;

        return $rep_start_date;
    }

    private function getDateDiff($date1, $date2)
    {
        //        $date1 = '2000-01-25';
        //        $date2 = '2010-02-20';

        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        return $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
    }

    public function rescheduling(Request $request)
    {
        $loanId = $request->get("loanId");
        $method = $request->get("method");
        $maturity_date = $request->get("matdate");

        print $this->UpdateRepaymentSchedule($loanId, ['method' => $method, 'maturity_date' => $maturity_date], 0, false);
        //        return back()->with('flash_success', 'Rescheduling done successfully');
    }

    public function enhancement(Request $request)
    {
        //var_dump($request->all());
        $loanId = $request->get("loanId");
        $enhancementDate = $request->get("enhdate");
        $amount = $request->get("amount");

        $LoanpaymentDue = \App\Models\LoanPaymentDue::where(['loan_id' => $loanId, 'due_date' => $enhancementDate])->first();
        if ($LoanpaymentDue) {
            \App\Models\LoanPaymentDue::where('id', $LoanpaymentDue->id)->update(['enhancement_amount' => $amount]);
            $debug = false;
            if ($debug) {
                print $this->UpdateRepaymentSchedule($loanId, [], $LoanpaymentDue->id, $debug);
            } else {
                $this->UpdateRepaymentSchedule($loanId, [], $LoanpaymentDue->id, $debug);
                $userId = \Illuminate\Support\Facades\Auth::user()->id;
                \App\Models\LoanModification::create([
                    'loan_id' => $loanId,
                    'modify_by' => $userId,
                    'due_date' => date("Y-m-d", strtotime($enhancementDate)),
                    'modification' => 'enhancement',
                    'amount' => $amount,
                ]);
                return back()->with('flash_success', 'Schedule enhancement done successfully');
            }
        } else {
            return back()->with('flash_error', 'Date you entered does not exist');
        }
    }

    function UpdateRepaymentSchedule($LoanId, $params = [], $id, $debug = false)
    {
        dd(23);
        $data = \App\Models\LoanHistory::where('id', $LoanId)->first();
        $loan_period = $data['loan_period'];
        if ($params) {
            //Rescheduling
            $method = $params['method'];
            $maturity_date = $params['maturity_date'];
            $DueData = \App\Models\LoanPaymentDue::where(['loan_id' => $LoanId, 'payment_status' => 1])->orderBy('id', 'desc')->first();
            if ($DueData) {
                $oustanding = $DueData['outstanding'];
                $disb_date = $DueData['due_date'];
                $rep_start_date = $this->getRepStartDate($disb_date);
                //$rep_start_date = $disb_date;
                $loan_period = $this->getDateDiff($disb_date, $maturity_date);
            } else {
                $oustanding = $data['total_amount_pr'];
                $disb_date = $data['disb_date'];
                $rep_start_date = $data['rep_start_date'];
                $loan_period = $this->getDateDiff($disb_date, $maturity_date);
            }
            \App\Models\LoanPaymentDue::where(['loan_id' => $LoanId, 'payment_status' => 0])->delete();
        } else {
            //Enhancement
            $DueData = \App\Models\LoanPaymentDue::where('id', $id)->first();
            $oustanding = ($DueData['outstanding'] + $DueData['enhancement_amount']);
            $installment_no = $DueData['installment_no'];
            $loan_period = $loan_period - $installment_no;
            $disb_date = $DueData['due_date'];
            $rep_start_date = $this->getRepStartDate($disb_date);
            //$rep_start_date = $disb_date;
            \App\Models\LoanPaymentDue::where('id', '>', $id)->where('loan_id', $LoanId)->delete();
        }

        //        dd($oustanding);
        $loan_freq = $data['loan_frequency'];
        $ChequeDate = $disb_date;

        $ApprovedLoanAmount = round($oustanding); //$amount_pr;
        $SrChargeRate = $data['kibor_rate'] + $data['spread_rate'];
        $LoanFrequency = $loan_freq;
        $LoanTerm = $loan_period;
        $RepStartDate = $rep_start_date;
        $loan_type_id = $data['loan_type_id'];
        $GrandPrinc = $GrandServ = $GrandTotal = $GrandDays = $GrandTakaful = 0;

        $iLoanFrequency = $LoanFrequency;
        $Return = "<table width='100%' border='1' bordercolor='#999999' cellspacing='0' cellpadding='4'>";
        $Return .= "<tr bgcolor='#CCCCCC'>"
            . "<td  align='center' rowspan='2'>Sr#</td>"
            . "<td align='center' rowspan='2'>Schedule Date</td>"
            . "<td align='center' rowspan='2'>Days</td>"
            . "<td  colspan='3' align='center'>Dues</td><td rowspan='2'  align='center'>Balance</td></tr>"
            . "<tr bgcolor='#CCCCCC'><td align='center'>Principle</td><td align='center'>Srv Charge</td><td align='center'>Takaful</td><td align='center'>Total</td></tr>";

        $Return .= "<tr><td colspan='6' align='right'></td><td align='right'>$ApprovedLoanAmount</td></tr>";

        $arryModeOfPayment = array(
            1 => 1,
            3 => 3,
            7 => 4,
            6 => 6,
            4 => $LoanTerm,
            8 => 12
        );
        $LoanFrequency = $arryModeOfPayment[$iLoanFrequency];
        $arryModeOfPaymentCalc = array(
            1 => 12,
            3 => 4,
            7 => 3,
            6 => 2,
            4 => 1,
            8 => 1
        );

        $SchedLoanTerm = $LoanTerm;
        $FormulaloanTerm = $LoanTerm / 12;
        $modeOfP = $arryModeOfPaymentCalc[$iLoanFrequency];

        $fv = 0;
        $pv = $ApprovedLoanAmount;
        $rate = ($SrChargeRate / 100) / 360 * 30;
        $nper = $LoanTerm;
        $type = 0;

        $PMT = ((0 - $pv * pow(1 + $rate, $nper)) /
            (1 + $rate) /
            ((pow(1 + $rate, $nper) - 1) / $rate)) * -1;

        $Fst = ($SrChargeRate / $modeOfP) / 100;
        $Snd = pow((1 + $Fst), ($modeOfP * $FormulaloanTerm));
        $Trd = 1 / $Snd;
        $Fth = 1 - $Trd;
        $Ffth = $Fth / $Fst;
        $Final = $ApprovedLoanAmount / $Ffth;

        $TotalSrCharge = $ApprovedLoanAmount * ($SrChargeRate / 100);
        $DailySrcCharge = $TotalSrCharge / 365;
        $TotalDaysLoanTerms = $SchedLoanTerm * (365 / 12);

        for ($i = 1; $i <= ($SchedLoanTerm / $LoanFrequency); $i++) {

            $AdditionalServiceCharges = 0;
            if ($i == 1) {
                $Dev_ScheduleDate = $RepStartDate;
                $datetime_chq = new DateTime(date("Y-m-d", strtotime($ChequeDate)));
                $datetime_repstart = new DateTime(date("Y-m-d", strtotime($RepStartDate)));
                $difference = $datetime_chq->diff($datetime_repstart);
            } else {
                $PreviousRepaymentDate = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate)));
                $date = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate)));
                $date->modify('+' . $LoanFrequency . ' month');
                $Dev_ScheduleDate = $date->format('Y-m-d');
                $NextRepaymentDate = new DateTime(date("Y-m-d", strtotime($Dev_ScheduleDate)));
                $difference = $PreviousRepaymentDate->diff($NextRepaymentDate);
            }
            $MonthlyServiceCharge = $ApprovedLoanAmount * $Fst;

            if ($loan_type_id != 1) {
                if ($i > 12) {
                    if ($i == 13) {
                        $Snd = pow((1 + $Fst), ($modeOfP * ($FormulaloanTerm - 1)));
                        $Trd = 1 / $Snd;
                        $Fth = 1 - $Trd;
                        $Ffth = $Fth / $Fst;
                        $Final = $ApprovedLoanAmount / $Ffth;
                        //$Final = ceil($Final);
                    }
                    $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
                } else {
                    $MonthlyPrinciple = 0;
                }
            } else {
                $MonthlyPrinciple = $Final - $MonthlyServiceCharge;
            }


            $MonthlyServiceCharge += $AdditionalServiceCharges;

            if (isset($takaful_amount) && $takaful_amount) {
                //echo $LoanTerm."/".$i."<br>";
                $lastTakaful = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 0])->orderBy('id', 'desc')->first();
                $lastTakafulLife = \App\Models\LoanTakaful::where(['loan_id' => $LoanId, 'type' => 1])->orderBy('id', 'desc')->first();
                if (($i == 1 || $i % 13 == 0) && $ApprovedLoanAmount >= 500) {
                    $startDate = $Dev_ScheduleDate;
                    $endDate = date('Y-m-d', strtotime($Dev_ScheduleDate . "+11 month "));
                    $renewalDate = date("Y-m-d", strtotime($endDate . "+1 day"));
                    $property_array = [
                        'loan_id' => $LoanId,
                        'type' => '0',
                        'covered_amount' => $ApprovedLoanAmount,
                        'start_date' => $Dev_ScheduleDate,
                        'end_date' => $endDate,
                        'renewal_date' => $renewalDate
                    ];
                    $life_array = [
                        'loan_id' => $LoanId,
                        'type' => '1',
                        'covered_amount' => $ApprovedLoanAmount,
                        'start_date' => $Dev_ScheduleDate,
                        'end_date' => $endDate,
                        'renewal_date' => $renewalDate
                    ];
                    //\App\Models\LoanTakaful::create($life_array);
                }
            }
            $ApprovedLoanAmount -= $MonthlyPrinciple;

            $DaysDiff = $difference->days;

            //$MonthlyPrinciple = round($MonthlyPrinciple);
            //$MonthlyServiceCharge = round($MonthlyServiceCharge);
            $MonthlyTakaful = 0;

            if ($i == ($SchedLoanTerm / $LoanFrequency)) {
                if ($ApprovedLoanAmount <> 0) {
                    $MonthlyPrinciple += $ApprovedLoanAmount;
                    $ApprovedLoanAmount = 0;
                }
            }

            $Total = $MonthlyPrinciple + $MonthlyServiceCharge + $MonthlyTakaful;

            $sScheduledRepaymentDate = date("M j, Y", strtotime($Dev_ScheduleDate));
            $sScheduledDay = date('D', strtotime($sScheduledRepaymentDate));
            $sScheduledDate = date('d', strtotime($sScheduledRepaymentDate));
            if ($sScheduledDay == "Sun") {
                $dateScheduledRepaymentDate = new DateTime(date("Y-m-d", strtotime($sScheduledRepaymentDate)));
                if ($sScheduledDate == 25) {
                    //$dateScheduledRepaymentDate->modify('-1 day');
                } else {
                    //$dateScheduledRepaymentDate->modify('+1 day');
                }
                $sScheduledRepaymentDate = $dateScheduledRepaymentDate->format('Y-m-d');
                $sScheduledRepaymentDate = date("M j, Y", strtotime($sScheduledRepaymentDate));
            }
            $MysqlScheduleDate = date("Y-m-d", strtotime($sScheduledRepaymentDate));

            $DueArray = [
                'loan_id' => $LoanId,
                'installment_no' => $i,
                'due_date' => $MysqlScheduleDate,
                'amount_total' => $Total,
                'amount_pr' => $MonthlyPrinciple,
                'outstanding' => $ApprovedLoanAmount,
                'amount_mu' => $MonthlyServiceCharge,
                'amount_takaful' => $MonthlyTakaful
            ];

            if (!$debug)
                \App\Models\LoanPaymentDue::create($DueArray);

            $Return .= "<tr>"
                . "<td>$i</td>"
                . "<td>$MysqlScheduleDate</td>"
                . "<td align='right'>$DaysDiff</td>"
                . "<td align='right'>$MonthlyPrinciple</td>"
                . "<td align='right'>$MonthlyServiceCharge</td>"
                . "<td align='right'>$MonthlyTakaful</td>"
                . "<td align='right'>" . ($Total) . "</td>"
                . "<td align='right'>" . ($ApprovedLoanAmount) . "</td>"
                . "</tr>";

            $GrandPrinc += $MonthlyPrinciple;
            $GrandServ += $MonthlyServiceCharge;
            $GrandTotal += $Total;
            $GrandDays += $DaysDiff;
            $GrandTakaful += $MonthlyTakaful;
        }

        $dLoanBalance = $GrandServ + $GrandPrinc;

        $Return .= "<tr>"
            . "<td colspan='3'>Total ($GrandDays Days)</td>"
            . "<td align='right'>$GrandPrinc</td>"
            . "<td align='right'>$GrandServ</td>"
            . "<td align='right'>$GrandTakaful</td>"
            . "<td align='right'>$GrandTotal</td>"
            . "<td align='right'>$ApprovedLoanAmount</td>"
            . "</tr>";
        $Return .= "</table>";
        return $Return;
    }


    public function loanList()
    {


        $data = \App\Models\LoanHistory::with(['loan_borrower', 'loan_payment_recovery'])->orderBy('disb_date', 'desc')->get();

        $processedData = $data->map(function ($item) {
            $no = $item->loan_payment_recovery->count() + 1;
            return [
                'id' => $item->id,
                'total_amount' => $item->total_amount,
                'total_amount_pr' => $item->total_amount_pr,
                'total_amount_mu' => $item->total_amount_mu,
                'account_no' => $item->account_no,
                'fname' => $item->loan_borrower->fname,
                'lname' => $item->loan_borrower->lname,
                'mname' => $item->loan_borrower->mname,
                'disb_date' => $item->disb_date,
                'loan_due' => \App\Models\LoanPaymentDue::where([['loan_id', '=', $item->id], ['installment_no', '=', $no]])->first(),
            ];
        });

        return response()->json([
            "data" => $processedData,
            "message" => "success"
        ], 200);
    }


    public function getLoanById(Request $request)
    {
        $data = \App\Models\LoanHistory::where('id', $request->id)->with(['loan_borrower', 'loan_payment_recovery'])->orderBy('disb_date', 'desc')->get();

        $processedData = $data->map(function ($item) {
            $item->loan_payment_recovery->count() + 1;
            return [
                'id' => $item->id,
                'total_amount' => $item->total_amount,
                'total_amount_pr' => $item->total_amount_pr,
                'total_amount_mu' => $item->total_amount_mu,
                'account_no' => $item->account_no,
                'fname' => $item->loan_borrower->fname,
                'lname' => $item->loan_borrower->lname,
                'mname' => $item->loan_borrower->mname,
                'disb_date' => $item->disb_date,

            ];
        });

        return response()->json([
            "data" => $processedData,
            "message" => "success"
        ], 200);
    }

    public function viewInstallmentDetail(Request $request)
    {

        $data = LoanPaymentRecovered::where([['loan_id', '=', $request->id]])->get();

        return response()->json([
            "data" => $data,
            "message" => "success"
        ], 200);
    }

    public function allinstallmentList(Request $request)
    {

        $data = LoanPaymentDue::where('payment_status', 1)->with('loan_payment_recovery')->get();
        return response()->json([
            "data" => $data,
            "message" => "success"
        ], 200);
    }


    public function getNextDueDate(Request $request)
    {

        $data = LoanPaymentDue::where('installment_no', $request->no)->value('due_date');
        return response()->json([
            "data" => $data,
            "message" => "success"
        ], 200);
    }
}
