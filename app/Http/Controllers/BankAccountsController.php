<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Validator;
use App\Models\FundTransfer;


class BankAccountsController extends Controller
{
    public function index()
    {
        $balance = 0;
        $bank_accounts = BankAccount::orderBy('created_at','desc')->get();
        return view('bank-accounts.bank-accounts')->with(['balance'=> $balance, 'bank_accounts'=> $bank_accounts]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_account' => 'required|not_in:0',
            'date' => 'required|date',
            'amount' => 'required',
            'type' => 'required|not_in:2',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        if($request->type == 1){

            $total_credit = BankAccount::where(['account'=> $request->bank_account, 'type'=> 0])->sum('amount');
            $total_debit = BankAccount::where(['account'=> $request->bank_account, 'type'=> 1])->sum('amount');

            if($request->amount > ($total_credit - $total_debit)){
                return redirect()->back()->with('warning', 'The amount exceeds the available balance.');
            }
        }

        $bank_account = new BankAccount();
        $bank_account->account = $request->bank_account;
        $bank_account->date = $request->date;
        $bank_account->amount = str_replace(',','',$request->amount);
        $bank_account->type = $request->type;
        $bank_account->save();

        return redirect()->back()->with('message', 'Saved Successfully');

    }

     public function bankDetails(Request $request){
        $bank_account_credit = (float)BankAccount::where(['account'=> $request->bank_account, 'type'=> 0])->sum('amount');
        $bank_account_debit = (float)BankAccount::where(['account'=> $request->bank_account, 'type'=> 1])->sum('amount');
        $funds = (float)FundTransfer::where(['bank_account'=> $request->bank_account, 'type'=> 1])->sum('amount');
        $bank_account_balance = ($bank_account_credit - $bank_account_debit) - $funds;
        // dd(($bank_account_credit - $bank_account_debit) , $funds);
        $response = [
            'data'=> number_format($bank_account_balance),
            'success' => true
        ];
        return response()->json($response);
    }
}
