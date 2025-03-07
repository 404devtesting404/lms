<?php

namespace App\Http\Controllers;

use App\Models\LoanBorrower;
use App\Helpers\Qs;
use App\Models\LoanBorrowerDocs;
use Illuminate\Http\Request;

class LoanDocController extends Controller {


    public function generateWelcomeLetter($loan_id){
        $loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        echo Qs::curl($loan_borrower,'welcome_letter.php');

    }

    public function generateAcknowledgementLetter($loan_id){
        \PhpOffice\PhpWord\TemplateProcessor::
        //$loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        $loan_borrower = \App\Models\LoanHistory::where('id', $loan_id)->with('loan_borrower')->first()->toArray();
        //dd($loan_borrower);
        $loan_borrower['ackowledgeDocsList'] = LoanBorrowerDocs::where('loan_id',$loan_id)->get()->toArray();
        echo Qs::curl($loan_borrower,'acknowledgment_letter.php');

    }


    public function AgreetoMortgage($loan_id){
        $loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        if(Qs::curl($loan_borrower) == 1){
            echo "err";
        }else{
            echo Qs::curl($loan_borrower);
        }

    }

    public function depositeDeeds($loan_id){
        $loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        if(Qs::curl($loan_borrower) == 1){
            echo "err";
        }else{
            echo Qs::curl($loan_borrower);
        }

    }

    public function UndertakingIndemnity($loan_id){
        $loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        if(Qs::curl($loan_borrower) == 1){
            echo "err";
        }else{
            echo Qs::curl($loan_borrower);
        }

    }

    public function UnderTakingFirstTime($loan_id){
        $loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        if(Qs::curl($loan_borrower) == 1){
            echo "err";
        }else{
            echo Qs::curl($loan_borrower);
        }

    }

    public function musharakaAgree($loan_id){
        $loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        if(Qs::curl($loan_borrower) == 1){
            echo "err";
        }else{
            echo Qs::curl($loan_borrower);
        }

    }

    public function promiseLease($loan_id){
        $loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        if(Qs::curl($loan_borrower) == 1){
            echo "err";
        }else{
            echo Qs::curl($loan_borrower);
        }

    }

    public function paymentAgree($loan_id){
        $loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        if(Qs::curl($loan_borrower) == 1){
            echo "err";
        }else{
            echo Qs::curl($loan_borrower);
        }

    }

    public function purchaseUnderTakingLetters($loan_id){
        $loan_borrower = LoanBorrower::where('id',$loan_id)->get()->toArray();
        if(Qs::curl($loan_borrower) == 1){
            echo "err";
        }else{
            echo Qs::curl($loan_borrower);
        }

    }


    public function uploadAcknowledgementDocForm($loan_id){


        $docs = LoanBorrowerDocs::where('loan_id',$loan_id)->get();
        return view('lms_loans.uploadAcknowledgementDocForm',compact('docs','loan_id'));

    }

    public function uploadAcknowledgementDocs($loan_id,Request $request){

        LoanBorrowerDocs::where('loan_id',$loan_id)->delete();
        $arr_name = $request->doc_name;
        foreach ($arr_name as $val):
            LoanBorrowerDocs::create(array('doc_name'=>$val,'loan_id'=>$loan_id,'doc_type'=>1,'timestamp'=>date('Y-m-d')));
        endforeach;

        return redirect()->route('loans.menu',$loan_id)
            ->with('flash_success', 'Docs name created successfully.');

    }



}
