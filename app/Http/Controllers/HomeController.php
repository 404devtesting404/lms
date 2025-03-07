<?php

namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Repositories\UserRepo;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected $user;

    public function __construct(UserRepo $user)
    {
        $this->user = $user;
    }


    public function index()
    {

        return redirect()->route('dashboard');
    }


    public function dashboard()
    {
        $loan_disburses = DB::table('loan_history as lh')
            ->leftJoin('loan_modifications as lm', 'lh.id', '=', 'lm.loan_id')
            ->select(
                'lh.id',
                DB::raw('lh.total_amount_pr as sum_total_amount_pr'),
                DB::raw('SUM(COALESCE(lm.amount, 0)) as sum_amount'),
                DB::raw('lh.total_amount_pr + SUM(COALESCE(lm.amount, 0)) as total_sum')
            )
            ->where('lh.loan_status_id', '<>', 4)
            ->groupBy('lh.id')
            ->get();
        // dd($loan_disburses);
        $loan_disbursed = $loan_disburses->sum('total_sum');
        $installment_recieved1 = \App\Models\LoanPaymentDue::where('payment_status', 1)->sum('amount_total');
        $installment_recieved2 = \App\Models\LoanPaymentRecovered::where('is_settle', 1)->sum('amount_total');

        $installment_recieved = $installment_recieved1 + $installment_recieved2;

        //        Profit Recieved
        $total_profit1 = \App\Models\LoanPaymentDue::where('payment_status', 1)->sum('amount_mu');
        $total_profit2 = \App\Models\LoanPaymentRecovered::where('is_settle', 1)->sum('amount_mu');

        $total_profit = $total_profit1 + $total_profit2;

        //        Principal Recieved
        $profit1 = \App\Models\LoanPaymentDue::where('payment_status', 1)->sum('amount_pr');
        $profit2 = \App\Models\LoanPaymentRecovered::where(['is_settle' => 1])->sum('amount_pr');
        $profit3 = \App\Models\LoanPaymentRecovered::where(['payment_type' => 2])->sum('amount_pr');


        $profit = $profit1 + $profit2 + $profit3;



        $loan_disbursed_count = \App\Models\LoanHistory::whereIn('loan_status_id', [10])->count();

        $loan_rejected = \App\Models\LoanHistory::whereIn('loan_status_id', [3, 6])->count();
        $early_settlement = \App\Models\LoanHistory::whereIn('loan_status_id', [7])->count();

        $dateto = date("Y-m-d");
        $early_settlement_amount = \App\Models\LoanPaymentRecovered::where(['is_settle' => 1])->sum('amount_pr');
        // dd($early_settlement_amount);

        $data = DB::table("loan_history")
            ->join("loan_borrowers", "loan_borrowers.id", "=", "loan_history.borrower_id")
            ->leftjoin("loan_modifications", function ($join) use ($dateto) {
                $join->on("loan_history.id", "=", "loan_modifications.loan_id");
                $join->where('loan_modifications.modification', 'enhancement');
                $join->where('loan_modifications.due_date', '<=', $dateto);
            })
            ->leftjoin("loan_payment_recovered as lpr", function ($join) {
                $join->on("loan_history.id", "=", "lpr.loan_id");
                $join->where("lpr.is_settle", 1);
            })
            ->leftJoin('loan_payment_due as lpd1', function ($join) use ($dateto) {
                $join->on('lpd1.loan_id', '=', 'loan_history.id');
            })
            ->where("loan_history.disb_date", "<=", DB::raw("'" . $dateto . "'"))
            ->where('loan_history.loan_status_id', '<>', 4)
            ->groupBy("lpd1.loan_id")
            ->select(
                DB::raw(
                    "loan_history.disb_date, "
                        . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_pr else 0 end) as amount_pr, "
                        . "sum(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.amount_mu else 0 end) as amount_mu, "
                        . "max(case when lpd1.payment_status = 1 or lpd1.payment_status IS NULL then lpd1.installment_no else null end) as inst_no, "
                        . "loan_modifications.amount as modification_amount, "
                        . "loan_borrowers.cnic, "
                        . "concat(loan_borrowers.fname,' ',loan_borrowers.mname,' ', loan_borrowers.lname) as 'name' "
                ),
                "loan_history.id as loan_id",
                "loan_history.total_amount_pr as finance_amount",
                DB::raw(
                    "
            CASE 
                WHEN COALESCE(
                    (SELECT lpd1_sub.outstanding 
                    FROM loan_payment_due lpd1_sub 
                    WHERE lpd1_sub.loan_id = lpd1.loan_id 
                        AND lpd1_sub.payment_status = 1 
                    ORDER BY lpd1_sub.due_date DESC 
                    LIMIT 1), 0) 
                - COALESCE(
                    (SELECT SUM(lpr2.amount_pr) 
                    FROM loan_payment_recovered lpr2 
                    WHERE lpr2.loan_id = loan_history.id 
                        AND lpr2.payment_type = 2), 0
                ) = 0 
                THEN loan_history.total_amount_pr
                ELSE 
                    COALESCE(
                        (SELECT lpd1_sub.outstanding 
                        FROM loan_payment_due lpd1_sub 
                        WHERE lpd1_sub.loan_id = lpd1.loan_id 
                            AND lpd1_sub.payment_status = 1 
                        ORDER BY lpd1_sub.due_date DESC 
                        LIMIT 1), 0) 
                    - COALESCE(
                        (SELECT SUM(lpr2.amount_pr) 
                        FROM loan_payment_recovered lpr2 
                        WHERE lpr2.loan_id = loan_history.id 
                            AND lpr2.payment_type = 2), 0
                    )
            END as outs_last_adjusted"
                )
            )
            ->where(function ($query) use ($dateto) {
                $query->where("lpr.recovered_date", ">=", DB::raw("'" . $dateto . "'"))
                    ->orWhereNull("lpr.recovered_date");
            })
            ->get();



        // dd($data);
        $outs = $data->sum('outs_last_adjusted');
        $modified_amount = $data->sum('modification_amount');
        $finance_amount = $data->where('outs', null)->sum('finance_amount');
        // $outstanding = $outs + $finance_amount + $modified_amount;
        $outstanding = $outs;

        $loan_cancled = DB::table('loan_history as lh')
            ->where('lh.loan_status_id', '=', 4)
            ->count();
        //    dd($loan_disbursed);


        /////////////START////////////

        $d['tt_records'] = \App\Models\LoanHistory::with([
            'loan_borrower',
            'loan_office',
            'loantype',
            'loan_modifications'
        ])
            ->select([
                'loan_history.*',
                DB::raw('(SELECT outstanding FROM loan_payment_due 
                      WHERE loan_payment_due.loan_id = loan_history.id 
                      AND payment_status = 1 
                      ORDER BY id DESC LIMIT 1) AS last_outstanding')
            ])
            ->orderBy('disb_date', 'desc')
            ->get()
            ->each(function ($loan) {
                $loan->total_modification_amount = $loan->total_modification_amount;
            });

        // ✅ **Total Outstanding Sum Calculation**
        $total_outstanding_sum = $d['tt_records']->sum(function ($loan) {
            if ($loan->last_outstanding > 0 && $loan->loan_status_id != 7) {
                return $loan->last_outstanding;
            } elseif ($loan->loan_status_id == 7) {
                return 0;
            } else {
                $LoanData = \App\Models\LoanHistory::where('id', $loan->id)
                    ->with('loan_borrower')
                    ->first();
                return $LoanData->total_amount_pr;
            }
        });


        /////////////END/////////////

        return view('pages.support_team.dashboard', compact('profit', 'total_outstanding_sum', 'total_profit', 'early_settlement_amount', 'early_settlement', 'loan_disbursed', 'installment_recieved', 'loan_disbursed_count', 'loan_rejected', 'outstanding', 'loan_cancled'));
    }

    public function test()
    {

        echo "<pre>";
        $aa = \App\Models\LoanHistory::pluck('borrower_id')->toArray();
        echo count($aa);
        echo "<br>";
        $bb = \App\Models\LoanBorrower::whereIn('id', $aa)->get()->toArray();
        print_r($bb);
        echo count($bb);

        $aa = \App\Models\LoanHistory::with(['loan_borrower', 'loan_office', 'loantype'])->get();
        foreach ($aa as $mc):
            echo "1";
            $mc->id;
            echo "<br>";
            echo $mc->loan_borrower->fname;
            // echo $mc->loan_borrower->fname." ".$mc->loan_borrower->mname." ".$mc->loan_borrower->lname;
            echo "<br>";
            echo $mc->loan_borrower->ltname;
            echo "<br>";
            echo $mc->loan_borrower->oname;
            echo "<br>";
            echo $mc->total_amount_pr;
            echo "<br>";
            echo $mc->total_amount_mu;
            echo "<br>";
            echo $mc->disb_date;
            echo "<br>";
            echo $mc->loan_status_id;
            echo "<br>";

        endforeach;
    }
}
