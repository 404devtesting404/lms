<?php


namespace App\Repositories;

use App\Models\LoanHistory;
use App\Models\LoanBorrower;
use \Illuminate\Support\Facades\DB;
class LoanHistoryRepo
{

    public function create($data)
    {
        return LoanHistory::create($data);
    }

    public function getAll($order = 'id')
    {
        $loans = DB::table('loan_history')
        ->select('loan_history.id', 'loan_history.disb_date', 'loan_history.total_amount_pr',
        'loan_history.total_amount_mu', 'loan_history.disb_date', 'loan_history.loan_status_id', 
                'loan_borrowers.fname', 'loan_borrowers.mname', 'loan_borrowers.lname',
                'loan_types.name as ltname', 'general_offices.name as oname')
        ->join('loan_types', 'loan_types.id', '=', 'loan_history.loan_type_id')
        ->join('general_offices', 'general_offices.id', '=', 'loan_history.office_id')
        ->join('loan_borrowers', 'loan_borrowers.id', '=', 'loan_history.borrower_id')
        ->orderBy('loan_borrowers.fname')
        ->get();
        return $loans;
        //return LoanHistory::orderBy($order)->with('loan_borrower')->get();
    }

    public function getDorm($data)
    {
        return LoanHistory::where($data)->get();
    }

    public function update($id, $data)
    {
        return LoanHistory::find($id)->update($data);
    }

    public function find($id)
    {
        return LoanHistory::find($id);
    }


}