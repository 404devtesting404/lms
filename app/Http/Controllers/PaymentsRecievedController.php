<?php

namespace App\Http\Controllers\Rest;

use App\Models\LoanBorrower;
use App\Helpers\Qs;
use App\Models\LoanBorrowerDocs;
use Illuminate\Http\Request;

class PaymentsRecievedController extends Controller {

    public function getInstallmentsPaid(){

        $data = \App\Models\LoanPaymentDue::where('payment',1)->get();
        return response()->json([
            'data'=>$data,
            "message" => "Already Disbursed"
        ], 200);
    }



}
