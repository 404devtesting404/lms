<?php

namespace App\Http\Controllers;

use App\Models\LoanPaymentDue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SendSmsController extends Controller
{
    public function dueInstallment()
    {
        $currentDate = Carbon::today();
        $oneDayBefore = Carbon::today()->addDays(1);
        $twoDaysAfter = Carbon::today()->subDays(2); // Due to the previous subtraction of 1 day
        $sevenDaysAfter = Carbon::today()->subDays(7); // Due to the previous addition of 3 days
//        dd($oneDayBefore, $twoDaysAfter, $sevenDaysAfter);

        $installmentsBefore = LoanPaymentDue::with('loan_history.loan_borrower')->where('due_date', $oneDayBefore)
            ->where(['due_status'=> 0, 'payment_status'=> 0, 'is_settle'=> 0])
            ->get();

        $installmentsAfter = LoanPaymentDue::with('loan_history.loan_borrower')->where('due_date', $twoDaysAfter)
            ->where(['due_status'=> 0, 'payment_status'=> 0, 'is_settle'=> 0])
            ->get();

        $installmentsSevenDaysAfter = LoanPaymentDue::with('loan_history.loan_borrower')->where('due_date', $sevenDaysAfter)
            ->where(['due_status'=> 0, 'payment_status'=> 0, 'is_settle'=> 0])
            ->get();

//        dd($installmentsBefore, $installmentsAfter, $installmentsSevenDaysAfter);

        foreach($installmentsBefore as $oneDayDue){
            $text = 'Dear Customer, Your Asaan Ghar Home Finance Monthly Installment Amount of Rs.' . number_format($oneDayDue->amount_total) . ' will be due on '. date('d-m-Y', strtotime($oneDayDue->due_date)) .'. If you already deposited, please ignore this message. Thank You.';
            $this->sendSms($text);
        }
        foreach($installmentsAfter as $twoDayDue){
            $text = 'Dear Customer, We would like to remind you that the Installment Amount of Rs.' . number_format($twoDayDue->amount_total) . ' was due on '. date('d-m-Y', strtotime($twoDayDue->due_date)) .'. To avoid reporting of overdue in ECIB, please pay your overdue amount. Thank You.';
            $this->sendSms($text);
        }
        foreach($installmentsSevenDaysAfter as $sevenDayDue){
            $text = 'Dear Customer, We have still not received the amount Rs.' . number_format($sevenDayDue->amount_total) . ' that was due on '. date('d-m-Y', strtotime($sevenDayDue->due_date)) .'. To avoid reporting of overdue in ECIB, please pay your overdue amount. Thank You.';
            $this->sendSms($text);
        }


    }

}
