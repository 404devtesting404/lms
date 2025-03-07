<?php

namespace App\Http\Controllers;

use App\Models\FundTransfer;
use App\Models\LoanHistory;
use Illuminate\Http\Request;

class FundsTransferController extends Controller
{
    public function index()
    {
        $balance = 0;
        $fund_transfers = FundTransfer::orderByDesc('date')->get();
        return view('funds-transfer.funds-transfer')->with(['balance'=> $balance, 'fund_transfers'=> $fund_transfers]);
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $arr = [];
        for ($i = 0; $i < count($data['account']); $i++) {
            $arr[] = [
                'account' => $data['account'][$i],
                'bank_account' => $data['bank_account'][$i],
                'type' => $data['type'][$i],
                'amount' => str_replace(',','',$data['amount'][$i]),
                'date' => $data['date'][$i],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
//        dd($arr);
        FundTransfer::insert($arr);
        return redirect()->back()->with('message', 'Saved Successfully');
    }

    public function loanUsers()
    {
        $html = '';
        $loan_histories = LoanHistory::with(['loan_borrower','loan_status'])
        ->where('loan_status_id','!=', 4)
        ->where('loan_status_id','!=', 7)
        ->get();
        foreach($loan_histories as $loan_history){
            $html .= '<option value="'.$loan_history->account_no.'">' . $loan_history->account_no.' ('.$loan_history->loan_borrower->fname.' '.$loan_history->loan_borrower->mname.' '.$loan_history->loan_borrower->lname.')</option>';
        }
        $response = [
            'data' => $html,
            'success' => true
        ];
        return response()->json($response);
    }
}
