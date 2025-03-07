<?php

namespace App\Http\Controllers;

use App\Models\FinChartOfAccount;
use App\Models\FinGeneralLedgerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller {

    public function __construct() {
        
    }

    public function TrialBalance() {
        //dd("Yeah");
        $d['report'] = "Financial";
        return view('reports.trialreport', $d);
    }

    public function TrialBalanceReport(Request $request) {
        error_reporting(E_ERROR);
        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date'
                ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;
        $ReportData = DB::table("fin_general_ledgers as gl")
                ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
                ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
                ->join("fin_chart_of_accounts as fca_l4", "fca_l4.code", "=", "fca_l5.parent_code")
                ->join("fin_chart_of_accounts as fca_l3", "fca_l3.code", "=", "fca_l4.parent_code")
                ->join("fin_chart_of_accounts as fca_l2", "fca_l2.code", "=", "fca_l3.parent_code")
                ->join("fin_chart_of_accounts as fca_l1", "fca_l1.code", "=", "fca_l2.parent_code")
                ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
                //    ->where('gld.coa_id','172')
                ->select(DB::raw("
                    fca_l1.code as 'L1_Code', fca_l1.title as 'L1_Title',
                    fca_l2.code as 'L2_Code', fca_l2.title as 'L2_Title',
                    fca_l3.code as 'L3_Code', fca_l3.title as 'L3_Title',
                    fca_l4.code as 'L4_Code', fca_l4.title as 'L4_Title',
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                    gld.debit,gld.credit, gl.txn_date, gl.id"))
                ->get();

        /*
         *    +"L1_Code": "01"
          +"L1_Title": "Assets"
          +"L2_Code": "01-01"
          +"L2_Title": "Non-Current Assets"
          +"L3_Code": "01-01-004"
          +"L3_Title": "Lendings"
          +"L4_Code": "01-01-004-001"
          +"L4_Title": "Lendings To Financial Institutions"
          +"L5_Code": "01-01-004-001-0001"
          +"L5_Title": "Lendings To Financial Institutions"
          +"debit": "9290000.00"
          +"credit": "0.00"
          +"txn_date": "2022-03-24"
          +"id": 1
         */

        $l1_array = array();
        $l2_array = array();
        $l3_array = array();
        $l4_array = array();
        $l5_array = array();
        $raja_array = array();
        if ($ReportData) {
            foreach ($ReportData as $reportrow) {
                $raja_array[$reportrow->L1_Code][$reportrow->L2_Code][$reportrow->L3_Code][$reportrow->L4_Code][$reportrow->L5_Code][] = $reportrow;
                $l1_array[$reportrow->L1_Code]['Debit'] += $reportrow->debit;
                $l1_array[$reportrow->L1_Code]['Credit'] += $reportrow->credit;
                $l1_array[$reportrow->L1_Code]['Title'] = $reportrow->L1_Title;

                $l2_array[$reportrow->L2_Code]['Debit'] += $reportrow->debit;
                $l2_array[$reportrow->L2_Code]['Credit'] += $reportrow->credit;
                $l2_array[$reportrow->L2_Code]['Title'] = $reportrow->L2_Title;

                $l3_array[$reportrow->L3_Code]['Debit'] += $reportrow->debit;
                $l3_array[$reportrow->L3_Code]['Credit'] += $reportrow->credit;
                $l3_array[$reportrow->L3_Code]['Title'] = $reportrow->L3_Title;

                $l4_array[$reportrow->L4_Code]['Debit'] += $reportrow->debit;
                $l4_array[$reportrow->L4_Code]['Credit'] += $reportrow->credit;
                $l4_array[$reportrow->L4_Code]['Title'] = $reportrow->L4_Title;

                $l5_array[$reportrow->L5_Code]['Debit'] += $reportrow->debit;
                $l5_array[$reportrow->L5_Code]['Credit'] += $reportrow->credit;
                $l5_array[$reportrow->L5_Code]['Title'] = $reportrow->L5_Title;
            }
        }

//        echo "<pre>";
//        print_r($l1_array);
//
//        die;
//        print_r($l2_array);
//        print_r($l3_array);
//        print_r($l4_array);
//        print_r($l5_array);
//
//        print_r($raja_array);
//        echo "</pre>";
//        dd($ReportData);

        $d['L1'] = $l1_array;
        $d['L2'] = $l2_array;
        $d['L3'] = $l3_array;
        $d['L4'] = $l4_array;
        $d['L5'] = $l5_array;
        $d['ReportData'] = $raja_array;

        return view('reports.trialreport', $d);
        //dd($request->all());
    }

    public function Financial() {
        //dd("Yeah");
        $d['report'] = "Financial";

        $d['chartOfAccounts'] = \App\Models\FinChartOfAccount::where('level', 'L5')->select('id', 'title', 'code')->orderBy("code")->get();

        return view('reports.finreport', $d);
    }

    public function FinancialReport(Request $request) {

        //dd($request->all());
        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date'
                ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['chartofaccount'] = $request->chartofaccount;
        $d['i'] = 0;

        /*
          select fca.code, fca.title, fca.parent_code, gld.debit,gld.debit, gl.txn_date
          from fin_general_ledgers as gl
          inner join fin_general_ledger_details as gld on gld.fin_gen_id = gl.id
          inner join fin_chart_of_accounts as fca on fca.id = gld.coa_id
          where gl.txn_date BETWEEN '' and ''
         */
        $ReportData = DB::table("fin_general_ledgers as gl")
                ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
                ->join("fin_chart_of_accounts as fca", "fca.id", "=", "gld.coa_id")
                ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
                ->where("gl.voucher_status", 3)
                ->select(DB::raw(""
                                . "fca.code, fca.title, fca.parent_code, gld.debit,gld.credit, gl.txn_date, gl.id, gl.txn_type, gl.txn_series"))
                ->get();
        if (isset($request->chartofaccount)) {
            $ReportData = DB::table("fin_general_ledgers as gl")
                    ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
                    ->join("fin_chart_of_accounts as fca", "fca.id", "=", "gld.coa_id")
                    ->whereBetween("gl.txn_date", array($request->datefrom, $request->dateto), "and")
                    ->where("gld.coa_id", $request->chartofaccount)
                    ->where("gl.voucher_status", 3)
                    ->select(DB::raw(""
                                    . "fca.code, fca.title, fca.parent_code, gld.debit,gld.credit, gl.txn_date, gl.id, gl.txn_type, gl.txn_series"))
                    ->get();
        }
        $raja_array = array();
        if ($ReportData) {
            foreach ($ReportData as $reportrow) {
                $raja_array[$reportrow->id][] = $reportrow;
            }
        }

        //dd($raja_array);
        $d['ReportData'] = $raja_array;

        return view('reports.finreport', $d);
        //dd($request->all());
    }

    public function trialreportacc() {
        //dd("Yeah");
        $d['report'] = "Financial";

        $d['chartOfAccounts'] = \App\Models\FinChartOfAccount::where('level', 'L1')->whereIn('code', [3, 5])->select('id', 'title', 'code')->orderBy("code")->get();

        return view('reports.trialreportacc', $d);
    }

    public function trialreportaccdetail(Request $request) {

        error_reporting(E_ERROR);
        $this->validate($request, [
            'chartofaccount' => 'required',
            'datefrom' => 'required|date',
            'dateto' => 'required|date'
                ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To', 'chartofaccount' => 'Chart of Account']);

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['chartofaccount'] = $request->chartofaccount;

        $ReportData = DB::table("loan_history")
                ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
                ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
                ->join("loan_payment_recovered", "loan_payment_due.id", "=", "loan_payment_recovered.due_id")
                ->join("general_offices", "general_offices.id", "=", "loan_history.office_id")
//                ->where("loan_payment_due.payment_status", "=", "1")
                ->whereBetween("loan_payment_recovered.recovered_date", [$request->datefrom, $request->dateto])
                ->orderBy("loan_payment_recovered.recovered_date")
                ->groupBy("loan_payment_due.id")
                ->select(DB::raw(""
                                . "concat(loan_borrowers.fname,' ',loan_borrowers.mname,' ', loan_borrowers.lname) as 'b_name', "
                                . "loan_borrowers.cnic, "
                                . "general_offices.name, "
                                . "loan_payment_due.installment_no, "
                                . "loan_payment_due.due_date, "
                                . "loan_payment_recovered.recovered_date, "
                                . "sum(loan_payment_recovered.amount_pr) as 'c_am_pr', "
                                . "sum(loan_payment_recovered.amount_mu) as 'c_am_mu'"
                                . ""), "loan_history.*")
                ->get();
        $d['ReportData'] = $ReportData;
        $d['i'] = 0;

        return view('reports.trialreportacc', $d);
        //dd($request->all());
    }

    public function Agingamount() {
        //dd("Yeah");
        $d['report'] = "Aging Amount Wise";
        return view('reports.agingamount', $d);
    }

    private function getDaysDiff($date1, $date2) {
        $date1 = date("Y-m-d", strtotime($date1));
        //echo "<br>" . $date1 . " - " . $date2 . "<br>";
        $now = strtotime($date2); // or your date as well
        $your_date = strtotime($date1);
        $datediff = $now - $your_date;

        $days_diff = round($datediff / (60 * 60 * 24));

        if ($days_diff <= 30) {
            return 30;
        } else if ($days_diff <= 60) {
            return 60;
        } else if ($days_diff <= 90) {
            return 90;
        } else if ($days_diff <= 120) {
            return 120;
        } else if ($days_diff <= 150) {
            return 150;
        } else if ($days_diff <= 180) {
            return 180;
        } else if ($days_diff <= 360) {
            return 360;
        } else {
            return 361;
        }
    }

    private function getPercentagesRange($range) {
        if ($range <= 20) {
            return 20;
        } else if ($range <= 40) {
            return 40;
        } else if ($range <= 60) {
            return 60;
        } else if ($range <= 80) {
            return 80;
        } else {
            return 81;
        }
    }

    private function getAmountRange($range) {
        if ($range <= 5000000) {
            return 5;
        } else if ($range <= 10000000) {
            return 10;
        } else if ($range <= 15000000) {
            return 15;
        } else if ($range <= 20000000) {
            return 20;
        } else if ($range <= 25000000) {
            return 25;
        } else {
            return 26;
        }
    }

    public function Agingamountreport(Request $request) {
        //dd("Yeah");
        error_reporting(E_ERROR);
        $d['report'] = "Aging Amount Wise";

        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date'
                ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $CurDate = date("Y-m-d");
        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['report_type'] = $request->report_type;
        $d['i'] = 0;

        $dateto = $request->dateto;
        $ReportData = DB::table("loan_history")
                //->leftJoin("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
                ->leftJoin('loan_payment_due', function ($join) use ($dateto) {
                    $join->on('loan_payment_due.loan_id', '=', 'loan_history.id');
                    $join->on('loan_payment_due.due_date', '<=', DB::raw("'" . $dateto . "'"));
                })
                ->leftJoin("general_offices", "general_offices.id", "=", "loan_history.office_id")
                ->where("loan_history.loan_status_id", "10")
                ->where("loan_history.id", "!=", "10")
                //->where("loan_history.id", "4")
                ->select("loan_history.id as loan_id", "loan_history.disb_date", "loan_payment_due.outstanding",
                        "loan_payment_due.due_date", "loan_history.total_amount_pr",
                        "loan_history.property_amount", "loan_history.office_id", "general_offices.name as office_name")
                ->get();

//        dd($ReportData);
        if ($ReportData) {

            $myAr = [];
            $total_fin = $total_out = [];
            $slabs_fin = $slabs_out = [];
            $slabs_percent = $slabs_percent_out = [];
            $slab_city = [];

            $total_rec = [];
            $total_mod = [];
            foreach ($ReportData as $row) {
                $myAr[$row->loan_id] = $row;

                //echo $row->loan_id;
                $RecData = \App\Models\LoanPaymentRecovered::where('recovered_date', '<=', DB::raw("'" . $dateto . "'"))
                        ->where('loan_id', $row->loan_id)
                        ->select(DB::raw("SUM(amount_pr) as total_rec"), DB::raw("MAX(recovered_date) as last_rec"))
                        ->first();

                $ModifiedAmount = \App\Models\LoanModification::where('loan_id', $row->loan_id)->where('modification', 'enhancement')->select(DB::raw('sum(amount) as amount'))->first();
//                if($row->loan_id==6){
//                    echo "<pre>";
//                    print_r($rec_record);
//                    echo "</pre>";
//                }

                $total_rec[$row->loan_id] = $RecData && $RecData->total_rec ? $RecData : 0;
                $total_mod[$row->loan_id] = $ModifiedAmount && $ModifiedAmount->amount ? $ModifiedAmount->amount : 0;
                $total_fin[$row->loan_id] = $row->total_amount_pr;
                $ttotal_out[$row->loan_id] = $row->outstanding;
            }
            //dd($total_rec);
//            dd($myAr);
            foreach ($ttotal_out as $loanId => $outstanding) {

                $loanRow = (array) $myAr[$loanId];
                $office_name = $loanRow['office_name'];
                $prop_amount = $loanRow['property_amount'];
                $disb_amount = $loanRow['total_amount_pr'];
                if (isset($total_mod[$loanId])) {
                    $disb_amount = $disb_amount + $total_mod[$loanId];
                }

                $disb_date = $loanRow['disb_date'];
                $due_date = $loanRow['due_date'];
                $outstanding = 0;
                if (!$outstanding) {
                    $outstanding = $disb_amount;
                }

                $rec_record = isset($total_rec[$loanId]) ? $total_rec[$loanId] : 0;
                if ($rec_record) {
                    $total_paid_pr = $rec_record['total_rec'];
                    $outstanding = $outstanding - $total_paid_pr;
                    $last_rec_date = $rec_record['last_rec'];
                } else {
                    $total_paid_pr = 0;
                    if ($due_date) {
                        $last_rec_date = $disb_date;
                    } else {
                        $last_rec_date = $dateto;
                    }
                }
                //echo $loanId;
                $days_diff = $this->getDaysDiff($last_rec_date, $dateto);

                // LTV Percent
                $ltv_percent = $disb_amount / $prop_amount * 100;
                $ltv_percent = $this->getPercentagesRange($ltv_percent);
                $slabs_percent_out[$days_diff]['slab_' . $ltv_percent] += $outstanding;
                $slabs_percent_out['disb']['disb_' . $ltv_percent] += $disb_amount;
                $slabs_percent_out[$days_diff]['cl_' . $ltv_percent] += 1;

                // Outstanding Amountwise
                $amount_range = $this->getAmountRange($outstanding);
                if ($amount_range == 5) {
                    //echo $loanId . ": " . $outstanding . " | " . $days_diff . " ( last rec: " . $last_rec_date . " - dateto: " . $dateto . " )" . "<br>";
                }
                $slabs_out[$days_diff]['slab_' . $amount_range] += $outstanding;
                $slabs_out['disb']['disb_' . $amount_range] += $disb_amount;
                $slabs_out[$days_diff]['cl_' . $amount_range] += 1;

                // Outstanding Citywise
                $slab_city[$office_name][$days_diff]['outs'] += $outstanding;
                $slab_city[$office_name]['disb'] += $disb_amount;
                $slab_city[$office_name][$days_diff]['cl'] += 1;
            }

//            echo "<pre>";
//            print_r($slabs_out);
//            print_r($slab_city);
//            print_r($slabs_percent_out);
//            print_r($myAr);
//            die;
        }
        $ReportData = $myAr;
//        dd($ReportData);
        $d['ReportData'] = $ReportData;
        $d['ReportRow'] = $slabs_out;
        $d['ReportRow_LTV'] = $slabs_percent_out;
        $d['slab_city'] = $slab_city;

        //echo "<pre>";
        //print_r($d);
        //die;
        return view('reports.agingamount', $d);
    }

    public function Dues() {
        //dd("Yeah");
        $d['report'] = "Dues";
        return view('reports.dues', $d);
    }

    public function DuesReport(Request $request) {
        $this->validate($request, [
            'datefrom' => 'required|date',
            'dateto' => 'required|date'
                ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);

        $CurDate = date("Y-m-d");
        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;

        $ReportData = DB::table("loan_history")
                ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
                ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
                ->join("general_offices", "general_offices.id", "=", "loan_history.office_id")
                ->where("loan_payment_due.payment_status", "=", "0")
                ->where("loan_history.id", "!=", "10")
//->whereRaw("DATE(gl.txn_date) BETWEEN ? AND ?", [$request->datefrom, $request->dateto])
                ->whereBetween("loan_payment_due.due_date", [$request->datefrom, $request->dateto])
                ->where("loan_payment_due.is_settle",0)
                            ->where("loan_payment_due.due_status",0)
                //->where("loan_payment_due.due_date","<=",$CurDate)
                ->groupBy("loan_payment_due.id")
                ->orderBy("loan_payment_due.due_date")
                ->select(DB::raw(""
                                . "concat(loan_borrowers.fname,' ',loan_borrowers.mname,' ', loan_borrowers.lname) as 'b_name', "
                                . "general_offices.name, "
                                . "loan_borrowers.cnic, "
                                . "loan_payment_due.due_date, "
                                . "loan_payment_due.installment_no, "
                                . "sum(loan_payment_due.amount_pr) as 'am_pr', "
                                . "sum(loan_payment_due.amount_mu) as 'am_mu'"
                                . ""), "loan_history.*")
                ->get();
        $d['ReportData'] = $ReportData;
        //dd($ReportData);
//        $loanBankslips = DB::table('loan_bankslips')
//            ->join('loan_payment_recovereds', 'loan_payment_recovereds.bank_slip_id', '=', 'loan_bankslips.id')
//            ->join('fin_banks_accounts', 'fin_banks_accounts.id', '=', 'loan_bankslips.bankAccountId')
//            ->groupBy('loan_bankslips.id')
//            ->select('loan_bankslips.*', 'fin_banks_accounts.bank_name', DB::raw('sum(loan_payment_recovereds.amount_total) as amount_sum'))
//
//            ->get();

        return view('reports.dues', $d);
        //dd($request->all());
    }

    public function Disb() {
        //dd("Yeah");
        $d['report'] = "Disbursement";
        return view('reports.disb', $d);
    }
    
    public function BookingReport($request)
    {


        $CurDate = date("Y-m-d");
        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $start_date = $request->datefrom;
        $end_date = $request->dateto;
        $d['i'] = 0;

        $ReportData = DB::table("loan_history")
            ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
            ->leftjoin("loan_modifications", function ($join) use ($end_date) {
                $join->on("loan_history.id", "=", "loan_modifications.loan_id");
                $join->where('loan_modifications.modification', 'enhancement');
                $join->where('loan_modifications.due_date', '<=' , $end_date);
            })
            ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
            ->join("loan_types", "loan_types.id", "=", "loan_history.loan_type_id")
            ->join("loan_status", "loan_status.id", "=", "loan_history.loan_status_id")
            ->whereBetween("loan_history.disb_date", [$request->datefrom, $request->dateto])
            ->groupBy("loan_history.id") // Should be grouped by loan_history.id if that's the unique identifier
            ->select(DB::raw(
                "loan_history.disb_date, " .
                "max(loan_payment_due.due_date) as maturity_date, " .
                "loan_payment_due.amount_total as amount_total, " .
                "concat(loan_borrowers.fname, ' ', loan_borrowers.mname, ' ', loan_borrowers.lname) as 'name', " .
                "loan_borrowers.cnic, " .
                "loan_history.id as loan_id, " .
                "loan_history.account_no, " .
                "loan_types.name as loan_type, " .
                "loan_history.loan_period, " .
                "loan_history.kibor_rate, " .
                "loan_history.spread_rate, " .
                "loan_history.total_amount_mu as rental_amount, " .
                // Summing loan_history.total_amount_pr and loan_modifications.amount
                "loan_history.total_amount_pr + COALESCE(loan_modifications.amount, 0) as finance_amount,".
                "loan_status.title as status"
            ))
            ->get();
       
        $d['ReportData'] = $ReportData;
        $ModifiedAmount = \App\Models\LoanModification::where('modification', 'enhancement')->select(DB::raw('sum(amount) as amount'), "loan_id")->groupBy('loan_id')->get();
        if (!empty($ModifiedAmount)) {
            foreach ($ModifiedAmount as $row) {
                $d['mod_amount'][$row->loan_id] = $row->amount;
            }
        }
        //echo "<pre>";
        //print_r($d);
        //die;
//        $loanBankslips = DB::table('loan_bankslips')
//            ->join('loan_payment_recovereds', 'loan_payment_recovereds.bank_slip_id', '=', 'loan_bankslips.id')
//            ->join('fin_banks_accounts', 'fin_banks_accounts.id', '=', 'loan_bankslips.bankAccountId')
//            ->groupBy('loan_bankslips.id')
//            ->select('loan_bankslips.*', 'fin_banks_accounts.bank_name', DB::raw('sum(loan_payment_recovereds.amount_total) as amount_sum'))
//
//            ->get();

        return view('reports.booking', $d);
        //dd($request->all());
    }

    public function DisbReport($request)
    {


        $CurDate = date("Y-m-d");
        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $start_date = $request->datefrom;
        $end_date = $request->dateto;
        $d['i'] = 0;

        $ReportData = DB::table("loan_history")
            ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
            ->leftjoin("loan_modifications", function ($join) use ($end_date) {
                $join->on("loan_history.id", "=", "loan_modifications.loan_id");
                $join->where('loan_modifications.modification', 'enhancement');
                $join->where('loan_modifications.due_date', '<=' , $end_date);
            })
            ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
            ->join("loan_types", "loan_types.id", "=", "loan_history.loan_type_id")
            ->join("loan_status", "loan_status.id", "=", "loan_history.loan_status_id")
                        ->where("loan_history.loan_status_id", "=", 10)
            ->whereBetween("loan_history.disb_date", [$request->datefrom, $request->dateto])
            ->groupBy("loan_history.id") // Should be grouped by loan_history.id if that's the unique identifier
            ->select(DB::raw(
                "loan_history.disb_date, " .
                "max(loan_payment_due.due_date) as maturity_date, " .
                "loan_payment_due.amount_total as amount_total, " .
                "concat(loan_borrowers.fname, ' ', loan_borrowers.mname, ' ', loan_borrowers.lname) as 'name', " .
                "loan_borrowers.cnic, " .
                "loan_history.id as loan_id, " .
                "loan_history.account_no, " .
                "loan_types.name as loan_type, " .
                "loan_history.loan_period, " .
                "loan_history.kibor_rate, " .
                "loan_history.spread_rate, " .
                "loan_history.total_amount_mu as rental_amount, " .
                // Summing loan_history.total_amount_pr and loan_modifications.amount
                "loan_history.total_amount_pr + COALESCE(loan_modifications.amount, 0) as finance_amount,".
                "loan_status.title as status"
            ))
            ->get();
//        dd($ReportData);
        $d['ReportData'] = $ReportData;
        $ModifiedAmount = \App\Models\LoanModification::where('modification', 'enhancement')->select(DB::raw('sum(amount) as amount'), "loan_id")->groupBy('loan_id')->get();
        if (!empty($ModifiedAmount)) {
            foreach ($ModifiedAmount as $row) {
                $d['mod_amount'][$row->loan_id] = $row->amount;
            }
        }
        //echo "<pre>";
        //print_r($d);
        //die;
//        $loanBankslips = DB::table('loan_bankslips')
//            ->join('loan_payment_recovereds', 'loan_payment_recovereds.bank_slip_id', '=', 'loan_bankslips.id')
//            ->join('fin_banks_accounts', 'fin_banks_accounts.id', '=', 'loan_bankslips.bankAccountId')
//            ->groupBy('loan_bankslips.id')
//            ->select('loan_bankslips.*', 'fin_banks_accounts.bank_name', DB::raw('sum(loan_payment_recovereds.amount_total) as amount_sum'))
//
//            ->get();

        return view('reports.disb', $d);
        //dd($request->all());
    }

    public function General() {
        //dd("Yeah");
        $d['report'] = "MIS Reports";
        return view('reports.general', $d);
    }

    public function Generalreport(Request $request) {
        $data = $request->all();
        $report_type = $data['report_type'];
        if ($report_type == 'rent' || $report_type == 'scrs') {
            $this->validate($request, [
                'dateto' => 'required|date'
                    ], [], ['dateto' => 'Date To']);
            $datefrom = "2022-10-01";
        } else {
            $this->validate($request, [
                'datefrom' => 'required|date',
                'dateto' => 'required|date'
                    ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);
            $datefrom = $data['datefrom'];
        }
//        $this->validate($request, [
//            'datefrom' => 'required|date',
//            'dateto' => 'required|date'
//                ], [], ['datefrom' => 'Date From', 'dateto' => 'Date To']);        

        $dateto = $data['dateto'];
        $report_type = $data['report_type'];
        switch ($report_type) {
            case "booking":
                return $this->BookingReport($request);
                break;
            case "disb":
                return $this->DisbReport($request);
                break;
            case "rep":
                return $this->PaymentsReport($request);
                break;
            case "due":
                return $this->DuesReport($request);
                break;
            case "od":
                return $this->OverDuesReport($request);
                break;
            case "rent":
                return $this->RentalDueReport($request);
                break;
            case "takprop":
                return $this->TakafulReport($request, 0);
                break;
            case "taklife":
                return $this->TakafulReport($request, 1);
                break;
            case "scrs":
                return $this->SCRSReport($request);
                break;
                 case "cancled":
                return $this->CancledReport($request);
                break;
            case "earlySettlement":
                return $this->EarlySettlementReport($request);
                break;
        }
    }
    
     public function EarlySettlementReport($request)
    {


        $CurDate = date("Y-m-d");
        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $start_date = $request->datefrom;
        $end_date = $request->dateto;
        $d['i'] = 0;

        $ReportData = DB::table("loan_history")
            ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
            ->leftjoin("loan_modifications", function ($join) use ($end_date) {
                $join->on("loan_history.id", "=", "loan_modifications.loan_id");
                $join->where('loan_modifications.modification', 'enhancement');
                $join->where('loan_modifications.due_date', '<=' , $end_date);
            })
            ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
            ->join("loan_types", "loan_types.id", "=", "loan_history.loan_type_id")
            ->join("loan_status", "loan_status.id", "=", "loan_history.loan_status_id")
            ->where("loan_history.loan_status_id", "=", 7)
            ->whereBetween("loan_history.disb_date", [$request->datefrom, $request->dateto])
            ->groupBy("loan_history.id") // Should be grouped by loan_history.id if that's the unique identifier
            ->select(DB::raw(
                "loan_history.disb_date, " .
                "max(loan_payment_due.due_date) as maturity_date, " .
                "loan_payment_due.amount_total as amount_total, " .
                "concat(loan_borrowers.fname, ' ', loan_borrowers.mname, ' ', loan_borrowers.lname) as 'name', " .
                "loan_borrowers.cnic, " .
                "loan_history.id as loan_id, " .
                "loan_history.account_no, " .
                "loan_types.name as loan_type, " .
                "loan_history.loan_period, " .
                "loan_history.kibor_rate, " .
                "loan_history.spread_rate, " .
                "loan_history.total_amount_mu as rental_amount, " .
                // Summing loan_history.total_amount_pr and loan_modifications.amount
                "loan_history.total_amount_pr + COALESCE(loan_modifications.amount, 0) as finance_amount,".
                "loan_status.title as status"
            ))
            ->get();
//        dd($ReportData);
        $d['ReportData'] = $ReportData;
        $ModifiedAmount = \App\Models\LoanModification::where('modification', 'enhancement')->select(DB::raw('sum(amount) as amount'), "loan_id")->groupBy('loan_id')->get();
        if (!empty($ModifiedAmount)) {
            foreach ($ModifiedAmount as $row) {
                $d['mod_amount'][$row->loan_id] = $row->amount;
            }
        }
        //echo "<pre>";
        //print_r($d);
        //die;
//        $loanBankslips = DB::table('loan_bankslips')
//            ->join('loan_payment_recovereds', 'loan_payment_recovereds.bank_slip_id', '=', 'loan_bankslips.id')
//            ->join('fin_banks_accounts', 'fin_banks_accounts.id', '=', 'loan_bankslips.bankAccountId')
//            ->groupBy('loan_bankslips.id')
//            ->select('loan_bankslips.*', 'fin_banks_accounts.bank_name', DB::raw('sum(loan_payment_recovereds.amount_total) as amount_sum'))
//
//            ->get();
        return view('reports.earlySettlement', $d);
        //dd($request->all());
    }

    public function CancledReport($request)
    {


        $CurDate = date("Y-m-d");
        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $start_date = $request->datefrom;
        $end_date = $request->dateto;
        $d['i'] = 0;

        $ReportData = DB::table("loan_history")
            ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
            ->leftjoin("loan_modifications", function ($join) use ($end_date) {
                $join->on("loan_history.id", "=", "loan_modifications.loan_id");
                $join->where('loan_modifications.modification', 'enhancement');
                $join->where('loan_modifications.due_date', '<=' , $end_date);
            })
            ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
            ->join("loan_types", "loan_types.id", "=", "loan_history.loan_type_id")
            ->join("loan_status", "loan_status.id", "=", "loan_history.loan_status_id")
            ->where("loan_history.loan_status_id", "=", 4)
            ->whereBetween("loan_history.disb_date", [$request->datefrom, $request->dateto])
            ->groupBy("loan_history.id") // Should be grouped by loan_history.id if that's the unique identifier
            ->select(DB::raw(
                "loan_history.disb_date, " .
                "max(loan_payment_due.due_date) as maturity_date, " .
                "loan_payment_due.amount_total as amount_total, " .
                "concat(loan_borrowers.fname, ' ', loan_borrowers.mname, ' ', loan_borrowers.lname) as 'name', " .
                "loan_borrowers.cnic, " .
                "loan_history.id as loan_id, " .
                "loan_history.account_no, " .
                "loan_types.name as loan_type, " .
                "loan_history.loan_period, " .
                "loan_history.kibor_rate, " .
                "loan_history.spread_rate, " .
                "loan_history.total_amount_mu as rental_amount, " .
                // Summing loan_history.total_amount_pr and loan_modifications.amount
                "loan_history.total_amount_pr + COALESCE(loan_modifications.amount, 0) as finance_amount,".
                "loan_status.title as status"
            ))
            ->get();
//        dd($ReportData);
        $d['ReportData'] = $ReportData;
        $ModifiedAmount = \App\Models\LoanModification::where('modification', 'enhancement')->select(DB::raw('sum(amount) as amount'), "loan_id")->groupBy('loan_id')->get();
        if (!empty($ModifiedAmount)) {
            foreach ($ModifiedAmount as $row) {
                $d['mod_amount'][$row->loan_id] = $row->amount;
            }
        }
        //echo "<pre>";
        //print_r($d);
        //die;
//        $loanBankslips = DB::table('loan_bankslips')
//            ->join('loan_payment_recovereds', 'loan_payment_recovereds.bank_slip_id', '=', 'loan_bankslips.id')
//            ->join('fin_banks_accounts', 'fin_banks_accounts.id', '=', 'loan_bankslips.bankAccountId')
//            ->groupBy('loan_bankslips.id')
//            ->select('loan_bankslips.*', 'fin_banks_accounts.bank_name', DB::raw('sum(loan_payment_recovereds.amount_total) as amount_sum'))
//
//            ->get();
        return view('reports.cancled', $d);
        //dd($request->all());
    }

    public function SCRSReport($request)
    {
        error_reporting(E_ERROR);
        $CurDate = date("Y-m-d");
        $d['datef'] = "2022-10-01";
        $d['datet'] = $request->dateto;
        $d['i'] = 0;
        $dateto = $request->dateto;

        $ReportData = DB::table("loan_history")
            ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
            ->leftjoin("loan_payment_recovered as lpr", function ($join) {
                $join->on("loan_history.id", "=", "lpr.loan_id");
                $join->where("lpr.is_settle", 1);
            })
            ->LeftJoin('loan_types', 'loan_types.id', '=', 'loan_history.loan_type_id')
            ->leftJoin('loan_payment_due as lpd1', function ($join) use ($dateto) {
                $join->on('lpd1.loan_id', '=', 'loan_history.id');
                $join->on(DB::raw('COALESCE(lpd1.payment_at, lpd1.due_date)'), '<=', DB::raw("'" . $dateto . "'"));
            })
            ->where("loan_history.disb_date", "<=", DB::raw("'" . $dateto . "'"))
            ->groupBy("lpd1.loan_id")
            ->select(DB::raw("loan_history.disb_date, "
                . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_pr else 0 end) as amount_pr, "
                . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_mu else 0 end) as amount_mu, "
                . "max(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.installment_no else null end) as inst_no, "
                . "min(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.outstanding else null end) as outs, "
//                . "sum(lpd1.amount_mu) as profit, "
                . "loan_borrowers.cnic, "
                . "CONCAT(
                        COALESCE(loan_borrowers.fname, ''),
                        CASE WHEN loan_borrowers.fname IS NOT NULL AND loan_borrowers.mname IS NOT NULL THEN ' ' ELSE '' END,
                        COALESCE(loan_borrowers.mname, ''),
                        CASE WHEN (loan_borrowers.fname IS NOT NULL OR loan_borrowers.mname IS NOT NULL) AND loan_borrowers.lname IS NOT NULL THEN ' ' ELSE '' END,
                        COALESCE(loan_borrowers.lname, '')
                    ) AS 'name'"
                . ""),
                "loan_history.id as loan_id",
                "loan_history.total_amount_pr as finance_amount",
                "loan_types.name as product_name",
                DB::raw('(SELECT amount_pr FROM loan_payment_due WHERE loan_id = loan_history.id ORDER BY id DESC LIMIT 1) as last_amount_pr'),
                DB::raw("(loan_history.kibor_rate + loan_history.spread_rate) as ror, loan_history.total_amount_pr as amount_sanctioned"),
                DB::raw("(SELECT SUM(CASE WHEN payment_at IS NULL THEN amount_mu ELSE 0 END + CASE WHEN payment_at IS NOT NULL AND due_date >= " . DB::raw("'" . $dateto . "'") . " THEN amount_mu ELSE 0 END) FROM loan_payment_due WHERE loan_id = loan_history.id) AS profit"),
                DB::raw("(SELECT MAX(due_date) from loan_payment_due WHERE loan_id = loan_history.id) as maturity_date ")
            )
            ->where(function ($query) use ($dateto) {
                $query->where("lpr.recovered_date", ">=", DB::raw("'" . $dateto . "'"))
                    ->orWhereNull("lpr.recovered_date"); // This ensures that records with a null recovered_date are also included
            })
            ->get();
        $ReportData1 = DB::table("loan_history")
            ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
            ->leftjoin("loan_payment_recovered as lpr", function ($join) {
                $join->on("loan_history.id", "=", "lpr.loan_id");
                $join->where("lpr.is_settle", 1);
            })
            ->LeftJoin('loan_types', 'loan_types.id', '=', 'loan_history.loan_type_id')
            ->leftJoin('loan_payment_due as lpd1', function ($join) use ($dateto) {
                $join->on('lpd1.loan_id', '=', 'loan_history.id');
            })
            ->where("loan_history.disb_date", "<=", DB::raw("'" . $dateto . "'"))
            ->groupBy("lpd1.loan_id")
            ->select(DB::raw("loan_history.disb_date, "
                . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_pr else 0 end) as amount_pr, "
                . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_mu else 0 end) as amount_mu, "
                . "max(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.installment_no else null end) as inst_no, "
                . "min(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.outstanding else null end) as outs, "
                . "loan_borrowers.cnic, "
                . "CONCAT(
                        COALESCE(loan_borrowers.fname, ''),
                        CASE WHEN loan_borrowers.fname IS NOT NULL AND loan_borrowers.mname IS NOT NULL THEN ' ' ELSE '' END,
                        COALESCE(loan_borrowers.mname, ''),
                        CASE WHEN (loan_borrowers.fname IS NOT NULL OR loan_borrowers.mname IS NOT NULL) AND loan_borrowers.lname IS NOT NULL THEN ' ' ELSE '' END,
                        COALESCE(loan_borrowers.lname, '')
                    ) AS 'name'"
                . ""),
                "loan_history.id as loan_id",
                "loan_history.total_amount_pr as finance_amount",
                "loan_types.name as product_name",
                DB::raw('(SELECT amount_pr FROM loan_payment_due WHERE loan_id = loan_history.id ORDER BY id DESC LIMIT 1) as last_amount_pr'),
                DB::raw("(loan_history.kibor_rate + loan_history.spread_rate) as ror, loan_history.total_amount_pr as amount_sanctioned"),
                DB::raw("(SELECT SUM(CASE WHEN payment_at IS NULL THEN amount_mu ELSE 0 END + CASE WHEN payment_at IS NOT NULL AND due_date >= " . DB::raw("'" . $dateto . "'") . " THEN amount_mu ELSE 0 END) FROM loan_payment_due WHERE loan_id = loan_history.id) AS profit"),
                DB::raw("(SELECT MAX(due_date) from loan_payment_due WHERE loan_id = loan_history.id) as maturity_date ")
            )
            ->where(function ($query) use ($dateto) {
                $query->where("lpr.recovered_date", ">=", DB::raw("'" . $dateto . "'"))
                    ->orWhereNull("lpr.recovered_date"); // This ensures that records with a null recovered_date are also included
            })
            ->get();
        // Merge the two collections based on loan_id
        $mergedData = $ReportData1->merge($ReportData);

        // Create a new array for entries that are not common between the two queries
        $uniqueData = $mergedData->groupBy('loan_id')->map(function ($items) {
            return $items->reduce(function ($result, $item) {
                return array_merge($result, (array) $item);
            }, []);
        })->values();

        // Optionally, you can convert the uniqueData collection to an array
        $uniqueArray = $uniqueData->toArray();
//        dd($uniqueArray);
        $d['ReportData'] = $uniqueArray;
        $ModifiedAmount = \App\Models\LoanModification::where('modification', 'enhancement')
            ->where('due_date', '<=', DB::raw("'" . $dateto . "'"))
            ->select(DB::raw('sum(amount) as amount'), "loan_id")->groupBy('loan_id')->get();
        if (!empty($ModifiedAmount)) {
            foreach ($ModifiedAmount as $row) {
                $d['mod_amount'][$row->loan_id] = $row->amount;
            }
        }
        $DueDetail = \App\Models\LoanPaymentDue::where('payment_status', '0')
            ->select(DB::raw(""
                . "min(amount_total) as amount_total, "
                . "min(due_date) as due_date"),
                "loan_id")
            //$DueDetail = \App\Models\LoanPaymentDue::select(DB::raw("min(amount_total) as amount_total, min(due_date) as due_date, max(outstanding) as outs"),"loan_id")
            //        ->where('due_date', '<=', DB::raw("'" . $dateto . "'"))
            ->groupBy('loan_id')->get();
        $d['due_detail'] = [];
        if (!empty($DueDetail)) {
            foreach ($DueDetail as $row) {
                $d['due_detail'][$row->loan_id] = $row;
            }
        }
//        echo "<pre>";
//        print_r($d);
//        die;
        return view('reports.scrs', $d);
    }

    public function TakafulReport($request, $type) {
        error_reporting(E_ERROR);
        $CurDate = date("Y-m-d");
        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;
        $datefrom = $request->datefrom;
        $dateto = $request->dateto;
        $ReportData = DB::table("loan_history")
                ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
                ->join('loan_takaful as lt', function ($join) use ($datefrom, $dateto, $type) {
                    $join->on('lt.loan_id', '=', 'loan_history.id');
                    $join->on('lt.start_date', '>=', DB::raw("'" . $datefrom . "'"));
                    $join->on('lt.start_date', '<=', DB::raw("'" . $dateto . "'"));
                    $join->on('lt.type', '=', DB::raw("'" . $type . "'"));
                })
                ->where("loan_history.id", "!=", "10")
//                ->where("loan_history.disb_date", "<=", DB::raw("'" . $dateto . "'"))
                ->where("loan_history.loan_status_id", "10")
                ->select(DB::raw("loan_history.disb_date, "
                                . "lt.covered_amount as outs, "
                                . "loan_borrowers.cnic, "
                                . "concat(loan_borrowers.fname,' ',loan_borrowers.mname,' ', loan_borrowers.lname) as 'name' "
                                . ""),
                        "loan_history.id as loan_id",
                        "loan_history.account_no",
                        "loan_history.total_amount_pr as finance_amount",
                        "lt.policy_numberr", "lt.start_date", "lt.end_date"
                )
                ->get();
        $ModifiedAmount = \App\Models\LoanModification::where('modification', 'enhancement')
                        ->where('due_date', '<=', DB::raw("'" . $dateto . "'"))
                        ->select(DB::raw('sum(amount) as amount'), "loan_id")->groupBy('loan_id')->get();
        if (!empty($ModifiedAmount)) {
            foreach ($ModifiedAmount as $row) {
                $d['mod_amount'][$row->loan_id] = $row->amount;
            }
        }
        $d['ReportData'] = $ReportData;
        return view('reports.takaful', $d);
    }

        // public function RentalDueReport($request)
//     {
//         error_reporting(E_ERROR);
//         $CurDate = date("Y-m-d");
//         $d['datef'] = "2022-10-01";
//         $d['datet'] = $request->dateto;
//         $d['i'] = 0;
//         $dateto = $request->dateto;

//         $ReportData = DB::table("loan_history")
//             ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
//             ->leftjoin("loan_payment_recovered as lpr", function ($join) {
//                 $join->on("loan_history.id", "=", "lpr.loan_id");
//                 $join->where("lpr.is_settle", 1);
//             })
//             ->leftJoin('loan_payment_due as lpd1', function ($join) use ($dateto) {
//                 $join->on('lpd1.loan_id', '=', 'loan_history.id');
// //                $join->on(DB::raw('COALESCE(lpd1.payment_at, lpd1.due_date)'), '<=', DB::raw("'" . $dateto . "'")); Removed this because farhan sohail was not showing
// //                    $join->on('lpd1.payment_status', '=', DB::raw("'1'"));
//             })
// //                ->where("loan_history.id", "!=", "10")
//             ->where("loan_history.disb_date", "<=", DB::raw("'" . $dateto . "'"))
// //                ->where("loan_history.loan_status_id", "10")
// //                ->whereBetween("loan_history.disb_date", [$request->datefrom, $request->dateto])
//             ->groupBy("lpd1.loan_id")
//             ->select(DB::raw("loan_history.disb_date, "
//                 . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_pr else 0 end) as amount_pr, "
//                 . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_mu else 0 end) as amount_mu, "
//                 . "max(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.installment_no else null end) as inst_no, "
//                 . "min(case when lpd1.payment_status = 1 then lpd1.outstanding else null end) as outs, "
//                 . "loan_borrowers.cnic, "
//                 . "CONCAT(
//                         COALESCE(loan_borrowers.fname, ''),
//                         CASE WHEN loan_borrowers.fname IS NOT NULL AND loan_borrowers.mname IS NOT NULL THEN ' ' ELSE '' END,
//                         COALESCE(loan_borrowers.mname, ''),
//                         CASE WHEN (loan_borrowers.fname IS NOT NULL OR loan_borrowers.mname IS NOT NULL) AND loan_borrowers.lname IS NOT NULL THEN ' ' ELSE '' END,
//                         COALESCE(loan_borrowers.lname, '')
//                     ) AS 'name'"
//                 . ""),
//                 "loan_history.id as loan_id",
//                 "loan_history.total_amount_pr as finance_amount"
//             )
//             ->where(function ($query) use ($dateto) {
//                 $query->where("lpr.recovered_date", ">=", DB::raw("'" . $dateto . "'"))
//                     ->orWhereNull("lpr.recovered_date"); // This ensures that records with a null recovered_date are also included
//             })
//             ->get();
// //        dd($ReportData);
//         $d['ReportData'] = $ReportData;
//         $ModifiedAmount = \App\Models\LoanModification::where('modification', 'enhancement')
//             ->where('due_date', '<=', DB::raw("'" . $dateto . "'"))
//             ->select(DB::raw('sum(amount) as amount'), "loan_id")->groupBy('loan_id')->get();
//         if (!empty($ModifiedAmount)) {
//             foreach ($ModifiedAmount as $row) {
//                 $d['mod_amount'][$row->loan_id] = $row->amount;
//             }
//         }
//         $DueDetail = \App\Models\LoanPaymentDue::where('payment_status', '0')
//             ->select(DB::raw(""
//                 . "min(amount_total) as amount_total, "
//                 . "min(due_date) as due_date"),
//                 "loan_id")
//             //$DueDetail = \App\Models\LoanPaymentDue::select(DB::raw("min(amount_total) as amount_total, min(due_date) as due_date, max(outstanding) as outs"),"loan_id")
//             //        ->where('due_date', '<=', DB::raw("'" . $dateto . "'"))
//             ->groupBy('loan_id')->get();
//         $d['due_detail'] = [];
//         if (!empty($DueDetail)) {
//             foreach ($DueDetail as $row) {
//                 $d['due_detail'][$row->loan_id] = $row;
//             }
//         }
// //        echo "<pre>";
// //        print_r($d);
// //        die;
//         return view('reports.rentaldue', $d);
//     }

 public function RentalDueReport($request)
    {
        error_reporting(E_ERROR);
        $CurDate = date("Y-m-d");
        $d['datef'] = "2022-10-01";
        $d['datet'] = $request->dateto;
        $d['i'] = 0;
        $dateto = $request->dateto;

        $ReportData = DB::table("loan_history")
            ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
            ->leftjoin("loan_payment_recovered as lpr", function ($join) {
                $join->on("loan_history.id", "=", "lpr.loan_id");
                $join->where("lpr.is_settle", 1);
            })
            ->leftJoin('loan_payment_due as lpd1', function ($join) use ($dateto) {
                $join->on('lpd1.loan_id', '=', 'loan_history.id');
                //$join->on(DB::raw('COALESCE(lpd1.payment_at, lpd1.due_date)'), '<=', DB::raw("'" . $dateto . "'")); //Removed this because farhan sohail was not showing
                $join->on(DB::raw('COALESCE(lpd1.payment_at, lpd1.due_date)'), '<=', DB::raw("'" . $dateto . "'"));
//                    $join->on('lpd1.payment_status', '=', DB::raw("'1'"));
            })
//                ->where("loan_history.id", "!=", "10")
            ->where("loan_history.disb_date", "<=", DB::raw("'" . $dateto . "'"))
            ->where("loan_history.loan_status_id", "<>", 4)
//                ->where("loan_history.loan_status_id", "10")
//                ->whereBetween("loan_history.disb_date", [$request->datefrom, $request->dateto])
            ->groupBy("lpd1.loan_id")
            ->select(DB::raw("loan_history.disb_date, "
                . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_pr else 0 end) as amount_pr, "
                . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_mu else 0 end) as amount_mu, "
                . "max(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.installment_no else null end) as inst_no, "
                . "min(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.outstanding else null end) as outs, "
                . "loan_borrowers.cnic, "
                . "CONCAT(
                        COALESCE(loan_borrowers.fname, ''),
                        CASE WHEN loan_borrowers.fname IS NOT NULL AND loan_borrowers.mname IS NOT NULL THEN ' ' ELSE '' END,
                        COALESCE(loan_borrowers.mname, ''),
                        CASE WHEN (loan_borrowers.fname IS NOT NULL OR loan_borrowers.mname IS NOT NULL) AND loan_borrowers.lname IS NOT NULL THEN ' ' ELSE '' END,
                        COALESCE(loan_borrowers.lname, '')
                    ) AS 'name'"
                . ""),
                "loan_history.id as loan_id",
                "loan_history.total_amount_pr as finance_amount",
                DB::raw('(SELECT amount_pr FROM loan_payment_due WHERE loan_id = loan_history.id ORDER BY id DESC LIMIT 1) as last_amount_pr')
            )
            ->where(function ($query) use ($dateto) {
                $query->where("lpr.recovered_date", ">=", DB::raw("'" . $dateto . "'"))
                    ->orWhereNull("lpr.recovered_date"); // This ensures that records with a null recovered_date are also included
            })
            ->get();
        $ReportData1 = DB::table("loan_history")
            ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
            ->leftjoin("loan_payment_recovered as lpr", function ($join) {
                $join->on("loan_history.id", "=", "lpr.loan_id");
                $join->where("lpr.is_settle", 1);
            })
            ->leftJoin('loan_payment_due as lpd1', function ($join) use ($dateto) {
                $join->on('lpd1.loan_id', '=', 'loan_history.id');
                //$join->on(DB::raw('COALESCE(lpd1.payment_at, lpd1.due_date)'), '<=', DB::raw("'" . $dateto . "'")); //Removed this because farhan sohail was not showing
//                $join->on(DB::raw('COALESCE(lpd1.payment_at, lpd1.due_date)'), '<=', DB::raw("'" . $dateto . "'"));
//                    $join->on('lpd1.payment_status', '=', DB::raw("'1'"));
            })
//                ->where("loan_history.id", "!=", "10")
            ->where("loan_history.disb_date", "<=", DB::raw("'" . $dateto . "'"))
            ->where("loan_history.loan_status_id", "<>", 4)
//                ->where("loan_history.loan_status_id", "10")
//                ->whereBetween("loan_history.disb_date", [$request->datefrom, $request->dateto])
            ->groupBy("lpd1.loan_id")
            ->select(DB::raw("loan_history.disb_date, "
                . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_pr else 0 end) as amount_pr, "
                . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_mu else 0 end) as amount_mu, "
                . "max(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.installment_no else null end) as inst_no, "
                . "min(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.outstanding else null end) as outs, "
                . "loan_borrowers.cnic, "
                . "CONCAT(
                        COALESCE(loan_borrowers.fname, ''),
                        CASE WHEN loan_borrowers.fname IS NOT NULL AND loan_borrowers.mname IS NOT NULL THEN ' ' ELSE '' END,
                        COALESCE(loan_borrowers.mname, ''),
                        CASE WHEN (loan_borrowers.fname IS NOT NULL OR loan_borrowers.mname IS NOT NULL) AND loan_borrowers.lname IS NOT NULL THEN ' ' ELSE '' END,
                        COALESCE(loan_borrowers.lname, '')
                    ) AS 'name'"
                . ""),
                "loan_history.id as loan_id",
                "loan_history.total_amount_pr as finance_amount",
                DB::raw('(SELECT amount_pr FROM loan_payment_due WHERE loan_id = loan_history.id ORDER BY id DESC LIMIT 1) as last_amount_pr')
            )
            ->where(function ($query) use ($dateto) {
                $query->where("lpr.recovered_date", ">=", DB::raw("'" . $dateto . "'"))
                    ->orWhereNull("lpr.recovered_date"); // This ensures that records with a null recovered_date are also included
            })
            ->get();
        // Merge the two collections based on loan_id
        $mergedData = $ReportData1->merge($ReportData);

// Create a new array for entries that are not common between the two queries
        $uniqueData = $mergedData->groupBy('loan_id')->map(function ($items) {
            return $items->reduce(function ($result, $item) {
                return array_merge($result, (array) $item);
            }, []);
        })->values();

// Optionally, you can convert the uniqueData collection to an array
        $uniqueArray = $uniqueData->toArray();
//        dd($ReportData, $ReportData1, $uniqueArray);
        $d['ReportData'] = $uniqueArray;
        $ModifiedAmount = \App\Models\LoanModification::where('modification', 'enhancement')
            ->where('due_date', '<=', DB::raw("'" . $dateto . "'"))
            ->select(DB::raw('sum(amount) as amount'), "loan_id")->groupBy('loan_id')->get();
        if (!empty($ModifiedAmount)) {
            foreach ($ModifiedAmount as $row) {
                $d['mod_amount'][$row->loan_id] = $row->amount;
            }
        }
        $DueDetail = \App\Models\LoanPaymentDue::where('payment_status', '0')
            ->select(DB::raw(""
                . "min(amount_total) as amount_total, "
                . "min(due_date) as due_date"),
                "loan_id")
            //$DueDetail = \App\Models\LoanPaymentDue::select(DB::raw("min(amount_total) as amount_total, min(due_date) as due_date, max(outstanding) as outs"),"loan_id")
            //        ->where('due_date', '<=', DB::raw("'" . $dateto . "'"))
            ->groupBy('loan_id')->get();
        $d['due_detail'] = [];
        if (!empty($DueDetail)) {
            foreach ($DueDetail as $row) {
                $d['due_detail'][$row->loan_id] = $row;
            }
        }
//        echo "<pre>";
//        print_r($d);
//        die;
        return view('reports.rentaldue', $d);
    }

    public function OverDues() {
        //dd("Yeah");
        $d['report'] = "OverDues";
        return view('reports.overdues', $d);
    }

    public function OverDuesReport($request) {
        $CurDate = date("Y-m-d");
        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;

        $ReportData = DB::table("loan_history")
                ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
                ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
                ->join("general_offices", "general_offices.id", "=", "loan_history.office_id")
                ->where("loan_payment_due.payment_status", "=", "0")
                ->where("loan_history.id", "!=", "7")
                ->where("loan_payment_due.is_settle",0)
                            ->where("loan_history.loan_status_id", "<>", 4)
            ->where("loan_payment_due.due_status",0)
                ->whereBetween("loan_payment_due.due_date", [$request->datefrom, $request->dateto])
                ->groupBy("loan_payment_due.id")
                ->select(DB::raw(""
                                . "concat(loan_borrowers.fname,' ',loan_borrowers.mname,' ', loan_borrowers.lname) as 'b_name', "
                                . "general_offices.name, "
                                . "loan_payment_due.due_date, "
                                . "sum(loan_payment_due.amount_pr) as 'am_pr', "
                                . "sum(loan_payment_due.amount_mu) as 'am_mu', "
                                . "DATEDIFF(NOW(), loan_payment_due.due_date) as 'days_until_now'"
                                . ""), "loan_history.*")
                ->get();
        $d['ReportData'] = $ReportData;
        //dd($ReportData);
//        $loanBankslips = DB::table('loan_bankslips')
//            ->join('loan_payment_recovereds', 'loan_payment_recovereds.bank_slip_id', '=', 'loan_bankslips.id')
//            ->join('fin_banks_accounts', 'fin_banks_accounts.id', '=', 'loan_bankslips.bankAccountId')
//            ->groupBy('loan_bankslips.id')
//            ->select('loan_bankslips.*', 'fin_banks_accounts.bank_name', DB::raw('sum(loan_payment_recovereds.amount_total) as amount_sum'))
//
//            ->get();

        return view('reports.overdues', $d);
        //dd($request->all());
    }

    public function Payments() {
        //dd("Yeah");
        $d['report'] = "Dues";
        return view('reports.payments', $d);
    }

    public function PaymentsReport($request) {

        $d['datef'] = $request->datefrom;
        $d['datet'] = $request->dateto;
        $d['i'] = 0;

        $ReportData = DB::table("loan_history")
                ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
                ->join("loan_payment_due", "loan_payment_due.loan_id", "=", "loan_history.id")
                ->join("loan_payment_recovered", "loan_payment_due.id", "=", "loan_payment_recovered.due_id")
                ->join("general_offices", "general_offices.id", "=", "loan_history.office_id")
//                ->where("loan_payment_due.payment_status", "=", "1")
                ->where("loan_history.id", "!=", "10")
                                ->where("loan_payment_due.due_status", "=", 0)

                ->whereBetween("loan_payment_recovered.recovered_date", [$request->datefrom, $request->dateto])
                ->orderBy("loan_payment_recovered.recovered_date")
                ->groupBy("loan_payment_due.id")
                ->select(DB::raw(""
                                . "concat(loan_borrowers.fname,' ',loan_borrowers.mname,' ', loan_borrowers.lname) as 'b_name', "
                                . "loan_borrowers.cnic, "
                                . "general_offices.name, "
                                . "loan_payment_due.installment_no, "
                                . "loan_payment_due.due_date, "
                                . "loan_payment_recovered.recovered_date, "
                                . "sum(loan_payment_due.amount_pr) as 'c_am_pr', "
                                . "sum(loan_payment_due.amount_mu) as 'c_am_mu'"
                                . ""), "loan_history.*")
                ->get();
        $d['ReportData'] = $ReportData;
//        dd($ReportData);
//        $loanBankslips = DB::table('loan_bankslips')
//            ->join('loan_payment_recovereds', 'loan_payment_recovereds.bank_slip_id', '=', 'loan_bankslips.id')
//            ->join('fin_banks_accounts', 'fin_banks_accounts.id', '=', 'loan_bankslips.bankAccountId')
//            ->groupBy('loan_bankslips.id')
//            ->select('loan_bankslips.*', 'fin_banks_accounts.bank_name', DB::raw('sum(loan_payment_recovereds.amount_total) as amount_sum'))
//
//            ->get();

        return view('reports.payments', $d);
        //dd($request->all());
    }

}
